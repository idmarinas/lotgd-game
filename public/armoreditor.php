<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

check_su_access(SU_EDIT_EQUIPMENT);

$values = [1 => 48, 225, 585, 990, 1575, 2250, 2790, 3420, 4230, 5040, 5850, 6840, 8010, 9000, 10350];

$translator = \LotgdKernel::get('translator');
$textDomain = 'grotto_armoreditor';
$armorlevel = (int) \LotgdRequest::getQuery('level');
$op = (string) \LotgdRequest::getQuery('op');
$id = (int) \LotgdRequest::getQuery('id');
$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Armor::class);

$params = [
    'textDomain' => $textDomain,
    'armorLevel' => $armorlevel
];

//-- Init page
\LotgdResponse::pageStart('title', ['level' => $armorlevel], $textDomain);

\LotgdNavigation::superuserGrottoNav();
\LotgdNavigation::addHeader('armoreditor.category.editor');
\LotgdNavigation::addNav('armoreditor.nav.editor', "armoreditor.php?level={$armorlevel}");
\LotgdNavigation::addNav('armoreditor.nav.armor.add', "armoreditor.php?op=add&level={$armorlevel}");

if ('edit' == $op || 'add' == $op)
{
    $params['tpl'] = 'edit';

    $lotgdFormFactory = \LotgdKernel::get('form.factory');
    $armorEntity = $repository->find($id);
    $armorEntity = $armorEntity ?: new \Lotgd\Core\Entity\Armor();

    if ($id === 0)
    {
        $armorEntity->setLevel($armorEntity->getLevel() ?: $armorlevel);
        $armorEntity->setDefense($repository->getNextDefenseLevel($armorEntity->getLevel()));
    }

    $form = $lotgdFormFactory->create(Lotgd\Core\EntityForm\ArmorType::class, $armorEntity, [
        'action' => "armoreditor.php?op=edit&id={$id}&level={$armorlevel}",
        'attr' => [
            'autocomplete' => 'off'
        ]
    ]);

    $form->handleRequest(\LotgdRequest::_i());

    if ($form->isSubmitted() && $form->isValid())
    {
        $entity = $form->getData();
        $entity->setValue($values[$entity->getDefense()]);

        \Doctrine::persist($entity);
        \Doctrine::flush();

        $message = ($id !== 0) ? 'armor.form.edit' : 'armor.form.new';

        $id = $entity->getArmorId();
        $armorlevel = $entity->getLevel();

        \LotgdFlashMessages::addSuccessMessage($translator->trans($message, ['name' => $entity->getArmorName()], $textDomain));

        //-- Redo form for change $level and set new data (generated IDs)
        $form = $lotgdFormFactory->create(Lotgd\Core\EntityForm\ArmorType::class, $entity, [
            'action' => "armoreditor.php?op=edit&id={$id}&level={$armorlevel}",
            'attr' => [
                'autocomplete' => 'off'
            ]
        ]);
    }
    \Doctrine::detach($armorEntity); //-- Avoid Doctrine save a invalid Form

    \LotgdNavigation::addNavAllow("armoreditor.php?op=edit&id={$id}&level={$armorlevel}");

    $params['form'] = $form->createView();
}
elseif ('del' == $op)
{
    $armor = $repository->find($id);

    \LotgdFlashMessages::addSuccessMessage($translator->trans('armor.form.del.success', ['id' => $id], $textDomain));
    \Doctrine::remove($armor);
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

\LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/armoreditor.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
