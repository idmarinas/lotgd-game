<?php

use Lotgd\Core\Entity\Weapons;
use Lotgd\Core\EntityForm\WeaponsType;

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

check_su_access(SU_EDIT_EQUIPMENT);

$values = [1 => 48, 225, 585, 990, 1575, 2250, 2790, 3420, 4230, 5040, 5850, 6840, 8010, 9000, 10350];

$op          = (string) LotgdRequest::getQuery('op');
$id          = (int) LotgdRequest::getQuery('id');
$weaponlevel = (int) LotgdRequest::getQuery('level');
$repository  = Doctrine::getRepository('LotgdCore:Weapons');

$textDomain = 'grotto_weaponeditor';

//-- Init page
LotgdResponse::pageStart('title', ['level' => $weaponlevel], $textDomain);

$params = [
    'textDomain'  => $textDomain,
    'weaponLevel' => $weaponlevel,
];

LotgdNavigation::superuserGrottoNav();

LotgdNavigation::addHeader('weaponeditor.category.editor');
LotgdNavigation::addNav('weaponeditor.nav.home', "weaponeditor.php?level={$weaponlevel}");

LotgdNavigation::addNav('weaponeditor.nav.weapon.add', "weaponeditor.php?op=add&level={$weaponlevel}");

if ('edit' == $op || 'add' == $op)
{
    $params['tpl'] = 'edit';

    $lotgdFormFactory = LotgdKernel::get('form.factory');
    $weaponEntity     = $repository->find($id);
    $weaponEntity     = $weaponEntity ?: new Weapons();

    if (0 === $id)
    {
        $weaponEntity->setLevel($weaponEntity->getLevel() ?: $weaponlevel);
        $weaponEntity->setDamage($repository->getNextDamageLevel($weaponEntity->getLevel()));
    }

    $form = $lotgdFormFactory->create(WeaponsType::class, $weaponEntity, [
        'action' => "weaponeditor.php?op=edit&id={$id}&level={$weaponlevel}",
        'attr'   => [
            'autocomplete' => 'off',
        ],
    ]);

    $form->handleRequest(LotgdRequest::_i());

    if ($form->isSubmitted() && $form->isValid())
    {
        $weaponEntity->setValue($values[$weaponEntity->getDamage()]);

        Doctrine::persist($weaponEntity);
        Doctrine::flush();

        $message = (0 !== $id) ? 'weapon.form.edit' : 'weapon.form.new';

        $id          = $weaponEntity->getWeaponid();
        $weaponlevel = $weaponEntity->getLevel();

        LotgdFlashMessages::addSuccessMessage(LotgdTranslator::t($message, ['name' => $weaponEntity->getWeaponname()], $textDomain));

        //-- Redo form for change $level and set new data (generated IDs)
        $form = $lotgdFormFactory->create(WeaponsType::class, $weaponEntity, [
            'action' => "weaponeditor.php?op=edit&id={$id}&level={$weaponlevel}",
            'attr'   => [
                'autocomplete' => 'off',
            ],
        ]);
    }
    Doctrine::detach($weaponEntity); //-- Avoid Doctrine save a invalid Form

    LotgdNavigation::addNavAllow("weaponeditor.php?op=edit&id={$id}&level={$weaponlevel}");

    $params['form'] = $form->createView();
}
elseif ('del' == $op)
{
    $armor = $repository->find($id);

    if ($armor)
    {
        LotgdFlashMessages::addSuccessMessage(LotgdTranslator::t('flash.message.del.success', ['id' => $id], $textDomain));
        Doctrine::remove($armor);
    }

    $op = '';
    LotgdRequest::setQuery('op', $op);
}

Doctrine::flush();

LotgdNavigation::addHeader('weaponeditor.category.weapon.level');
//-- Max level (DragonKills) of weapon created
$max = $repository->getMaxWeaponLevel();
for ($i = 0; $i <= $max; ++$i)
{
    LotgdNavigation::addNav('weaponeditor.nav.weapon.level', "weaponeditor.php?level={$i}", [
        'params' => ['n' => $i],
    ]);
}

$params['weapons'] = $repository->findByLevel($weaponlevel, ['damage' => 'ASC']);

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/weaponeditor.html.twig', $params));

//-- Finalize page
LotgdResponse::pageEnd();
