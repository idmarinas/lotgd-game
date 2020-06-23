<?php

// Initially written as a module by Chris Vorndran.
// Moved into core by JT Traub

require_once 'common.php';

check_su_access(SU_EDIT_CREATURES);

$textDomain = 'grotto-masters';

$op = (string) \LotgdHttp::getQuery('op');
$masterId = (int) (string) \LotgdHttp::getQuery('master_id');

page_header('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
    'masterId' => $masterId
];

$repository = \Doctrine::getRepository('LotgdCore:Masters');

\LotgdNavigation::superuserGrottoNav();

if ('del' == $op)
{
    $master = $repository->find($masterId);

    $type = 'addErrorMessage';
    $message = 'flash.message.del.error';
    if ($master)
    {
        \Doctrine::remove($master);
        \Doctrine::flush();

        $type = 'addSuccessMessage';
        $message = 'flash.message.del.success';
    }

    \LotgdFlashMessages::{$type}(\LotgdTranslator::t($message, [], $textDomain));

    return redirect('masters.php');
}
elseif ('edit' == $op)
{
    $params['tpl'] = 'edit';

    \Lotgdnavigation::addHeader('masters.category.functions');
    \Lotgdnavigation::addNav('masters.nav.return', 'masters.php');

    $masterEntity = $repository->find($masterId);
    $masterEntity = $masterEntity ?: new \Lotgd\Core\Entity\Masters();
    \Doctrine::detach($masterEntity);

    $form = LotgdForm::create(Lotgd\Core\EntityForm\MastersType::class, $masterEntity, [
        'action' => "masters.php?op=edit&master_id={$masterId}",
        'attr' => [
            'autocomplete' => 'off'
        ]
    ]);

    $form->handleRequest();

    if ($form->isSubmitted() && $form->isValid())
    {
        $entity = $form->getData();
        $method = $entity->getCreatureid() ? 'merge' : 'persist';

        \Doctrine::{$method}($entity);
        \Doctrine::flush();

        $masterId = $entity->getCreatureid();

        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.save.success', [ 'name' => $entity->getCreaturename()], $textDomain));

        //-- Redo form for change $masterId and set new data (generated IDs)
        $form = LotgdForm::create(Lotgd\Core\EntityForm\MastersType::class, $entity, [
            'action' => "masters.php?op=edit&master_id={$masterId}",
            'attr' => [
                'autocomplete' => 'off'
            ]
        ]);
    }

    \LotgdNavigation::addNavAllow("masters.php?op=edit&master_id={$masterId}");

    $params['form'] = $form->createView();
}

if ('' == $op)
{
    $params['tpl'] = 'default';

    \Lotgdnavigation::addHeader('masters.category.functions');
    \Lotgdnavigation::addNav('masters.nav.refresh', 'masters.php');
    \Lotgdnavigation::addNav('masters.nav.add', 'masters.php?op=edit');

    $params['masters'] = $repository->findAll();
}

rawoutput(\LotgdTheme::renderLotgdTemplate('core/page/masters.twig', $params));

page_footer();
