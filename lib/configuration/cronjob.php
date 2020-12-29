<?php

use Lotgd\Core\Form\CronjobType;

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

        \LotgdKernel::get('core.cronjobs.cache')->delete('cronjobstable');

        \Doctrine::remove($cronEntity);
        \Doctrine::flush();
    }

    $op = '';
}

if ('' == $op)
{
    $lotgdFormFactory = \LotgdLocator::get('Lotgd\Core\SymfonyForm');

    $form = $lotgdFormFactory->create(CronjobType::class, [
        'newdaycron' => getsetting('newdaycron', 0),
    ], [
        'action' => 'configuration.php?setting=cronjob&save=save',
        'attr'   => [
            'autocomplete' => 'off',
        ],
    ]);

    $form->handleRequest();

    if ($form->isSubmitted() && $form->isValid())
    {
        $messageType  = null;
        $formIsValid  = true;
        $postSettings = $form->getData();

        require_once 'lib/configuration/save.php';

        if ($messageType)
        {
            \LotgdFlashMessages::{$messageType}(\LotgdTranslator::t($message, [], 'app-form'));
        }
    }

    $query = $repository->createQueryBuilder('u');

    $params['paginator'] = $repository->getPaginator($query, $page);
    $params['form']      = $form->createView();
}
elseif ('newcronjob' == $op)
{
    $params['tpl'] = 'cronjob-new';

    $lotgdFormFactory = \LotgdLocator::get('Lotgd\Core\SymfonyForm');
    $entity           = $repository->find($cronId);
    $entity           = $entity ?: new \Lotgd\Core\Entity\Cronjob();
    \Doctrine::detach($entity);

    $form = $lotgdFormFactory->create(Lotgd\Core\EntityForm\CronjobType::class, $entity, [
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

        \LotgdKernel::get('core.cronjobs.cache')->delete('cronjobstable');

        //-- Redo form for change $cronId and set new data (generated IDs)
        $form = $lotgdFormFactory->create(Lotgd\Core\EntityForm\CronjobType::class, $entity, [
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
