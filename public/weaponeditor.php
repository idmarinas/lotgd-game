<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/showform.php';

check_su_access(SU_EDIT_EQUIPMENT);

$weaponarray = [
    'Weapon,title',
    'weaponid' => 'Weapon ID,hidden',
    'level' => 'DK Level,int',
    'weaponname' => 'Weapon Name',
    'damage' => 'Damage,range,1,15,1'
];
$values = [1 => 48, 225, 585, 990, 1575, 2250, 2790, 3420, 4230, 5040, 5850, 6840, 8010, 9000, 10350];

$op = (string) \LotgdHttp::getQuery('op');
$id = (int) \LotgdHttp::getQuery('id');
$weaponlevel = (int) \LotgdHttp::getQuery('level');
$repository = \Doctrine::getRepository('LotgdCore:Weapons');

$textDomain = 'page-weaponeditor';

page_header('title', ['level' => $weaponlevel], $textDomain);

$params = [
    'textDomain' => $textDomain,
    'weaponLevel' => $weaponlevel
];

\LotgdNavigation::superuserGrottoNav();

\LotgdNavigation::addHeader('weaponeditor.category.editor');
\LotgdNavigation::addNav('weaponeditor.nav.home', "weaponeditor.php?level=$weaponlevel");

\LotgdNavigation::addNav('weaponeditor.nav.weapon.add', "weaponeditor.php?op=add&level=$weaponlevel");

if ('edit' == $op || 'add' == $op)
{
    $params['tpl'] = 'edit';

    $weapon = ['damage' => $repository->getNextDamageLevel($weaponlevel)];
    $weapon = $repository->find($id);
    $weapon = $repository->extractEntity($weapon);
    $weapon['level'] = ($weapon['level'] >= 0) ? $weapon['level'] : $weaponlevel;

    $params['form'] = lotgd_showform($weaponarray, $weapon, true, false, false);
}
elseif ('del' == $op)
{
    $armor = $repository->find($id);

    if ($armor)
    {
        \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.del.success', ['id' => $id], $textDomain));
        \Doctrine::remove($armor);
    }

    $op = '';
    \LotgdHttp::setQuery('op', $op);
}
elseif ('save' == $op)
{
    $post = \LotgdHttp::getPostAll();
    $post['value'] = $values[$post['damage']];
    $post['level'] = ($post['level'] >= 0) ? $post['level'] : $weaponlevel;

    $weapon = $repository->find($post['weaponid']);
    $weapon = $repository->hydrateEntity($post, $weapon);

    \Doctrine::persist($weapon);

    \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('weapon.form.edit', ['name' => $post['weaponname']], $textDomain));

    $op = '';
    \LotgdHttp::setQuery('op', $op);
}

\Doctrine::flush();

\LotgdNavigation::addHeader('weaponeditor.category.weapon.level');
//-- Max level (DragonKills) of weapon created
$max = $repository->getMaxWeaponLevel();
for ($i = 0; $i <= $max; $i++)
{
    \LotgdNavigation::addNav('weaponeditor.nav.weapon.level', "weaponeditor.php?level={$i}", [
        'params' => ['n' => $i]
    ]);
}

$params['weapons'] = $repository->findByLevel($weaponlevel, ['damage' => 'ASC']);

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/weaponeditor.twig', $params));

page_footer();
