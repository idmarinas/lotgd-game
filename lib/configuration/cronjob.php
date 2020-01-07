<?php

require_once 'lib/configuration/save.php';

$op = (string) \LotgdHttp::getQuery('op');
$cronId = (string) \LotgdHttp::getQuery('cronid');

$params['cronId'] = $cronId;

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Cronjob::class);

if ('savecronjob' == $op)
{
    $post = \LotgdHttp::getPostAll();

    //-- NAME - only accept alphabetic characters and digits in the unicode "letter" and "number" categories, respectively
    $filter = new \Zend\I18n\Filter\Alnum();
    $post['name'] = $filter->filter($post['name']);
    //-- SCHELUDE - modifies to remove whitespaces from the beginning and end.
    $post['schedule'] = trim($post['schedule']);

    $cronEntity = $repository->hydrateEntity($post);
    $gameLogMessage = "`@Create CronJob`0 `^{$post['name']}`0`$ by admin {$session['user']['playername']}`0";
    $message = 'flash.message.cronjob.created';
    $messageType = 'addSuccessMessage';

    $paramsFlashMessages = [];

    if ($cronId)
    {
        $cronEntity = $repository->find($cronId);
        $cronEntity = $repository->hydrateEntity($post, $cronEntity);

        $message = 'flash.message.cronjob.updated';
        $gameLogMessage = "`qUpdate CronJob`0 `^{$post['name']}`0`$ by admin {$session['user']['playername']}`0";
    }
    //-- If is a new cron check if name exist
    else
    {
        //-- Check if clan name is taken
        $noExistValidator = new \Lotgd\Core\Validator\Db\NoObjectExists ([
            'object_repository' => $repository,
            'fields'   => 'name',
        ]);

        if (! $noExistValidator->isValid($post['name']))
        {
            $paramsFlashMessages['name'] = $post['name'];
            $message = 'flash.message.cronjob.name.exist';
            $messageType = 'addWarningMessage';
            $cronEntity = null;

            $op = 'newcronjob';
        }
    }

    \LotgdFlashMessages::{$messageType}(\LotgdTranslator::t($message, $paramsFlashMessages, $textDomain));

    if ($cronEntity)
    {
        gamelog($gameLogMessage, 'cronjob');

        \Doctrine::persist($cronEntity);
        \Doctrine::flush();

        $op = '';
    }

    $cronCache = \LotgdLocator::get('Cache\Core\Cronjob');
    $cronId->removeItem('cronjobstable');
}
elseif ('delcronjob' == $op)
{
    $cronEntity = $repository->find($cronId);

    if ($cronEntity)
    {
        gamelog("`4Delete CronJob `^$cronId`4 by admin {$session['user']['playername']}", 'cronjob');

        \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.cronjob.deleted', [], $textDomain));

        $cronCache = \LotgdLocator::get('Cache\Core\Cronjob');
        $cronId->removeItem('cronjobstable');

        \Doctrine::remove($cronEntity);
        \Doctrine::flush();
    }

    $op = '';
}

if ('' == $op)
{
    $setup_cronjob = include_once 'lib/data/configuration_cronjob.php';

    $page = (int) \LotgdHttp::getQuery('page', 1);

    $query = $repository->createQueryBuilder('u');

    $params['paginator'] = $repository->getPaginator($query, $page);

    $params['form'] = lotgd_showform($setup_cronjob, ['newdaycron' => getsetting('newdaycron', 0)], false, false, false);
}
elseif ('newcronjob' == $op)
{
    $params['tpl'] = 'cronjob-new';

    require_once 'lib/listfiles.php';

    $data = [
        'mailer' => 'sendmail',
        'smtpSender' => 'jobby@localhost',
        'smtpSenderName' => 'Jobby',
        'dateFormat' => 'Y-m-d H:i:s',
        'debut' => 0,
        'enabled' => 1
    ];

    if ($cronid)
    {
        $cronEntity = $repository->find($cronId);
        $result = $repository->extractEntity($cronEntity);

        if ($result)
        {
            $data = $result;
        }
    }

    $sort = list_files('cronjob', []);
    sort($sort);
    $scriptenum = implode('', $sort);
    $scriptenum = ',,none'.$scriptenum;

    $form = [
        'Job requires these,title',
        'Note: This three options are mandatory,note',
        'name' => 'Name for CronJob. There can not be another with the same name',
        'schedule' => 'Crontab schedule format (man -s 5 crontab) or DateTime format (Y-m-d H:i:s) (https://crontab.guru)',
        'command' => 'The shell command to run,enum'.$scriptenum,
        'Note: if you want add new CronJob add your Cron PHP file in "cronjob/" folder,note',
        'Other,title',
        'runAs' => 'Run as this user, if crontab user has sudo privileges',
        'debug' => 'Send jobby internal messages to "debug.log",bool',
        'Filtering,title',
        'environment' => 'Development environment for this job,textarea',
        'runOnHost' => 'Run jobs only on this hostname',
        'maxRuntime' => 'Maximum execution time for this job (in seconds),number',
        'enabled' => 'Run this job at scheduled times (enable or disabled),bool',
        'haltDir' => 'A job will not run if this directory contains a file bearing the job\'s name',
        'Logging,title',
        'output' => 'Redirect stdout and stderr to this file',
        'dateFormat' => 'Format for dates on jobby log messages',
        'Mailing,title',
        'recipients' => 'Comma-separated string of email addresses',
        'mailer' => 'Email method: sendmail or smtp or mail',
        'smtpHost' => 'SMTP host; if mailer is smtp',
        'smtpPort' => 'SMTP port; if mailer is smtp,number',
        'smtpUsername' => 'SMTP user; if mailer is smtp',
        'smtpPassword' => 'SMTP password; if mailer is smtp',
        'smtpSecurity' => 'SMTP security option: ssl or tls, if mailer is smtp',
        'smtpSender' => 'The sender and from addresses used in SMTP notices',
        'smtpSenderName' => 'Jobby	The name used in the from field for SMTP messages',
    ];

    $params['form'] = lotgd_showform($form, $data, false, false, false);
}
