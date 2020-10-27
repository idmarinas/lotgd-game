<?php

$op     = (string) \LotgdRequest::getQuery('op');
$cronId = (int) \LotgdRequest::getQuery('cronid');
$page   = (int) \LotgdRequest::getQuery('page', 1);

$params['cronId'] = $cronId;

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Cronjob::class);

if ('delcronjob' == $op)
{
    $cronEntity = $repository->find($cronId);

    if ($cronEntity)
    {
        gamelog("`4Delete CronJob `^{$cronId}`4 by admin {$session['user']['playername']}", 'cronjob');

        \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.cronjob.deleted', [], $textDomain));

        $cronCache = \LotgdLocator::get('Cache\Core\Cronjob');
        $cronCache->removeItem('cronjobstable');

        \Doctrine::remove($cronEntity);
        \Doctrine::flush();
    }

    $op = '';
}

if ('' == $op)
{
    $form = \LotgdLocator::get('Lotgd\Core\Form\Cronjob');

    if (\LotgdRequest::isPost())
    {
        $postSettings = \LotgdRequest::getPostAll();
        $form->setData($postSettings);

        $messageType = 'addErrorMessage';
        $message     = 'flash.message.error';

        if ($form->isValid())
        {
            $messageType  = null;
            $postSettings = $form->getData();

            require_once 'lib/configuration/save.php';
        }

        if ($messageType)
        {
            \LotgdFlashMessages::{$messageType}(\LotgdTranslator::t($message, [], 'app-form'));
        }
    }

    $query = $repository->createQueryBuilder('u');

    if ( ! \LotgdRequest::isPost())
    {
        $form->setData(['newdaycron' => getsetting('newdaycron', 0)]);
    }

    $params['paginator'] = $repository->getPaginator($query, $page);
    $params['form']      = $form;
}
elseif ('newcronjob' == $op)
{
    $params['tpl'] = 'cronjob-new';

    $entity = $repository->find($cronId);
    $entity = $entity ?: new \Lotgd\Core\Entity\Cronjob();
    \Doctrine::detach($entity);

    $form = LotgdForm::create(Lotgd\Core\EntityForm\CronjobType::class, $entity, [
        'action' => "configuration.php?setting=cronjob&op=newcronjob&cronid={$cronId}",
        'attr'   => [
            'autocomplete' => 'off',
        ],
    ]);

    $form->handleRequest();

    $paramsFlashMessages = [];
    $message             = null;
    $messageType         = 'addSuccessMessage';

    if ($form->isSubmitted() && $form->isValid())
    {
        $entity  = $form->getData();
        $method  = $cronId ? 'merge' : 'persist';
        $message = $cronId ? 'flash.message.cronjob.updated' : 'flash.message.cronjob.created';

        \Doctrine::{$method}($entity);
        \Doctrine::flush();

        $cronId = $entity->getId();

        gamelog('`@'.$cronId ? 'Updated' : 'Create'." CronJob`0 `^{$entity->getName()}`0`$ by admin {$session['user']['playername']}`0", 'cronjob');

        $cronCache = \LotgdLocator::get('Cache\Core\Cronjob');
        $cronCache->removeItem('cronjobstable');

        //-- Redo form for change $cronId and set new data (generated IDs)
        $form = LotgdForm::create(Lotgd\Core\EntityForm\CronjobType::class, $entity, [
            'action' => "configuration.php?setting=cronjob&op=newcronjob&cronid={$cronId}",
            'attr'   => [
                'autocomplete' => 'off',
            ],
        ]);

        if ($message)
        {
            \LotgdFlashMessages::{$messageType}(\LotgdTranslator::t($message, $paramsFlashMessages, $textDomain));
        }
    }

    //-- In this position can updated $cronId var
    \LotgdNavigation::addNavAllow("configuration.php?setting=cronjob&op=newcronjob&cronid={$cronId}");

    $params['form'] = $form->createView();
}
