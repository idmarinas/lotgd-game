<?php

use Lotgd\Core\Entity\Masters;
use Lotgd\Core\EntityForm\MastersType;

// Initially written as a module by Chris Vorndran.
// Moved into core by JT Traub

require_once 'common.php';

check_su_access(SU_EDIT_CREATURES);

$textDomain = 'grotto_masters';

$op       = (string) LotgdRequest::getQuery('op');
$masterId = (int) (string) LotgdRequest::getQuery('master_id');

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
    'masterId'   => $masterId,
];

$repository = Doctrine::getRepository('LotgdCore:Masters');

LotgdNavigation::superuserGrottoNav();

if ('del' == $op)
{
    $master = $repository->find($masterId);

    $type    = 'addErrorMessage';
    $message = 'flash.message.del.error';
    if ($master)
    {
        Doctrine::remove($master);
        Doctrine::flush();

        $type    = 'addSuccessMessage';
        $message = 'flash.message.del.success';
    }

    LotgdFlashMessages::{$type}(LotgdTranslator::t($message, [], $textDomain));

    redirect('masters.php');
}
elseif ('edit' == $op)
{
    $params['tpl'] = 'edit';

    Lotgdnavigation::addHeader('masters.category.functions');
    Lotgdnavigation::addNav('masters.nav.return', 'masters.php');

    $lotgdFormFactory = LotgdKernel::get('form.factory');
    $masterEntity     = $repository->find($masterId);
    $masterEntity     = $masterEntity ?: new Masters();

    $form = $lotgdFormFactory->create(MastersType::class, $masterEntity, [
        'action' => "masters.php?op=edit&master_id={$masterId}",
        'attr'   => [
            'autocomplete' => 'off',
        ],
    ]);

    $form->handleRequest(LotgdRequest::_i());

    if ($form->isSubmitted() && $form->isValid())
    {
        $entity = $form->getData();

        Doctrine::persist($entity);
        Doctrine::flush();

        $masterId = $entity->getCreatureid();

        LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('flash.message.save.success', ['name' => $entity->getCreaturename()], $textDomain));

        //-- Redo form for change $masterId and set new data (generated IDs)
        $form = $lotgdFormFactory->create(MastersType::class, $entity, [
            'action' => "masters.php?op=edit&master_id={$masterId}",
            'attr'   => [
                'autocomplete' => 'off',
            ],
        ]);
    }
    Doctrine::detach($masterEntity); //-- Avoid Doctrine save a invalid Form

    LotgdNavigation::addNavAllow("masters.php?op=edit&master_id={$masterId}");

    $params['form'] = $form->createView();
}

if ('' == $op)
{
    $params['tpl'] = 'default';

    Lotgdnavigation::addHeader('masters.category.functions');
    Lotgdnavigation::addNav('masters.nav.refresh', 'masters.php');
    Lotgdnavigation::addNav('masters.nav.add', 'masters.php?op=edit');

    $params['masters'] = $repository->findAll();
}

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/masters.html.twig', $params));

//-- Finalize page
LotgdResponse::pageEnd();
