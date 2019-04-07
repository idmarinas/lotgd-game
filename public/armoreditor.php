<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/showform.php';

check_su_access(SU_EDIT_EQUIPMENT);

tlschema('armor');

$armorarray = [
    'Armor,title',
    'armorid' => 'Armor ID,hidden',
    'armorname' => 'Armor Name',
    'defense' => 'Defense,range,1,15,1'
];
$values = [1 => 48, 225, 585, 990, 1575, 2250, 2790, 3420, 4230, 5040, 5850, 6840, 8010, 9000, 10350];

$textDomain = 'page-armoreditor';
$armorlevel = (int) \LotgdHttp::getQuery('level');
$op = (string) \LotgdHttp::getQuery('op');
$id = (int) \LotgdHttp::getQuery('id');
$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Armor::class);

$params = [
    'armorLevel' => $armorlevel
];

page_header('title', ['level' => $armorlevel], $textDomain);

\LotgdNavigation::superuserGrottoNav();
\LotgdNavigation::addHeader('armoreditor.category.editor');
\LotgdNavigation::addNav('armoreditor.nav.editor', "armoreditor.php?level={$armorlevel}");
\LotgdNavigation::addNav('armoreditor.nav.armor.add', "armoreditor.php?op=add&level={$armorlevel}");

if ('edit' == $op || 'add' == $op)
{
    $armor = ['defense' => $repository->getNextDefenseLevel($armorlevel)];
    if ('edit' == $op)
    {
        $armor = $repository->find($id);
        $hydrator = new \Zend\Hydrator\ClassMethods();
        $armor = $hydrator->extract($armor);
    }

    $params['form'] = lotgd_showform($armorarray, $armor, true, false, false);

    rawoutput(LotgdTheme::renderLotgdTemplate('core/page/armoreditor/add-edit.twig', $params));

    page_footer();
}
elseif ('del' == $op)
{
    $armor = $repository->find($id);

    \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('armor.form.del.success', ['id' => $id], $textDomain));
    \Doctrine::remove($armor);
    \Doctrine::flush();
}
elseif ('save' == $op)
{
    $armorId = (int) \LotgdHttp::getPost('armorid');
    $armorname = \LotgdHttp::getPost('armorname');
    $defense = \LotgdHttp::getPost('defense');

    $armor = $repository->find($armorId);

    $message = 'armor.form.edit';
    if (! $armor)
    {
        $armor = new \Lotgd\Core\Entity\Armor();
        $message = 'armor.form.new';
    }
    $armor->setLevel($armorlevel)
        ->setDefense($defense)
        ->setArmorname($armorname)
        ->setValue($values[$defense])
    ;

    \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t($message, ['name' => $armorname], $textDomain));

    \Doctrine::persist($armor);
    \Doctrine::flush();
}

\LotgdNavigation::addHeader('armoreditor.category.armor.level');
//-- Max level (DragonKills) of armor created
$max = $repository->getMaxArmorLevel();
for ($i = 0; $i <= $max; $i++)
{
    \LotgdNavigation::addNav('armoreditor.nav.armor.level', "armoreditor.php?level=$i", [
        'params' => ['n' => $i]
    ]);
}

$params['armors'] = $repository->findByLevel($armorlevel, ['defense' => 'ASC']);

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/armoreditor.twig', $params));

page_footer();
