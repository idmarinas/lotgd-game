<?php

// Initially written as a module by Chris Vorndran.
// Moved into core by JT Traub

require_once 'common.php';

check_su_access(SU_EDIT_CREATURES);

$textDomain = 'page-masters';

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
elseif ('save' == $op)
{
    $form = \LotgdHttp::getPostAll();

    $master = $repository->find($masterId);
    $master = $repository->hydrateEntity($form, $master);

    \Doctrine::persist($master);
    \Doctrine::flush();

    \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.save.success', [ 'name' => $master->getCreaturename()], $textDomain));

    return redirect('masters.php');
}
elseif ('edit' == $op)
{
    $params['tpl'] = 'edit';
    $params['maxLevel'] = getsetting('maxlevel');

    \Lotgdnavigation::addHeader('masters.category.functions');
    \Lotgdnavigation::addNav('masters.nav.return', 'masters.php');

    $master = $repository->find($masterId);

    $params['master'] = $master ? $repository->extractEntity($master) : [
        'creaturelevel' => 1,
        'creaturename' => '',
        'creatureweapon' => '',
        'creaturewin' => '',
        'creaturelose' => ''
    ] ;
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
