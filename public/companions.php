<?php

// addnews ready
// mail ready
// translator ready

// hilarious copy of mounts.php
require_once 'common.php';
require_once 'lib/showform.php';

check_su_access(SU_EDIT_MOUNTS);

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Companions::class);

$textDomain = 'grotto-companions';
$hydrator = new \Zend\Hydrator\ClassMethods();

$params = [
    'textDomain' => $textDomain
];

page_header('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();

\LotgdNavigation::addHeader('companions.category.editor');
\LotgdNavigation::addNav('companions.nav.add', 'companions.php?op=add');

$op = (string) \LotgdHttp::getQuery('op');
$id = (int) \LotgdHttp::getQuery('id');

if ('deactivate' == $op)
{
    $companionEntity = $repository->find($id);
    $companionEntity->setCompanionactive(0);

    \Doctrine::persist($companionEntity);

    $op = '';
    \LotgdHttp::setQuery('op', '');

    LotgdCache::removeItem("companionsdata-{$id}");
}
elseif ('activate' == $op)
{
    $companionEntity = $repository->find($id);
    $companionEntity->setCompanionactive(1);

    \Doctrine::persist($companionEntity);

    $op = '';
    \LotgdHttp::setQuery('op', '');

    LotgdCache::removeItem("companiondata-{$id}");
}
elseif ('del' == $op)
{
    $companionEntity = $repository->find($id);

    \Doctrine::remove($companionEntity);

    $op = '';
    \LotgdHttp::setQuery('op', '');

    module_delete_objprefs('companions', $id);
    LotgdCache::removeItem("companiondata-$id");
}
elseif ('take' == $op)
{
    $companionEntity = $repository->find($id);

    $row = $hydrator->extract($companionEntity);

    if ($row)
    {
        $row['attack'] = $row['attack'] + $row['attackperlevel'] * $session['user']['level'];
        $row['defense'] = $row['defense'] + $row['defenseperlevel'] * $session['user']['level'];
        $row['maxhitpoints'] = $row['maxhitpoints'] + $row['maxhitpointsperlevel'] * $session['user']['level'];
        $row['hitpoints'] = $row['maxhitpoints'];

        $row = modulehook('alter-companion', $row);

        require_once 'lib/buffs.php';

        $message = 'flash.message.take.fail';
        $paramsMessage = [];
        if (apply_companion($row['name'], $row))
        {
            $message = 'flash.message.take.success';
            $paramsMessage = ['name' => $row['name']];
        }

        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t($message, $paramsMessage, $textDomain));
    }

    $op = '';
    \LotgdHttp::setQuery('op', '');
}
elseif ('save' == $op)
{
    $subop = (string) \LotgdHttp::getQuery('subop');

    if ('module' == $subop)
    {
        // Save modules settings
        $module = (string) \LotgdHttp::getQuery('module');
        $post = \LotgdHttp::getPostAll();
        reset($post);

        foreach ($post as $key => $val)
        {
            set_module_objpref('companions', $id, $key, $val, $module);
        }

        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));
    }

    $op =  $id ? 'edit' : '';
    \LotgdHttp::setQuery('op', $op);
}

unset($companionEntity);
\Doctrine::flush();

if ('' == $op)
{
    $params['tpl'] = 'default';

    $params['companionsList'] = $repository->findBy([], ['category' => 'DESC', 'name' => 'DESC']);
}
elseif ('edit' == $op || 'add' == $op)
{
    $params['tpl'] = 'edit';

    \LotgdNavigation::addNav('companions.nav.editor', 'companions.php');
    \LotgdNavigation::addNav('companions.nav.properties', "companions.php?op=edit&id={$id}");

    module_editor_navs('prefs-companions', "companions.php?op=edit&subop=module&id={$id}&module=");

    $subop = (string) \LotgdHttp::getQuery('subop');

    if ('module' == $subop)
    {
        $module = (string) \LotgdHttp::getQuery('module');

        rawoutput("<form action='companions.php?op=save&subop=module&id={$id}&module={$module}' method='POST'>");
        module_objpref_edit('companions', $module, $id);
        rawoutput('</form>');

        \LotgdNavigation::addNavAllow("companions.php?op=save&subop=module&id={$id}&module={$module}");

        page_footer();
    }
    else
    {
        $companionEntity = $repository->find($id);
        $creatureArray = $companionEntity ? $repository->extractEntity($companionEntity) : [];
        $companionEntity = $companionEntity ?: new \Lotgd\Core\Entity\Companions();
        \Doctrine::detach($companionEntity);

        $form = LotgdForm::create(Lotgd\Core\EntityForm\CompanionsType::class, $companionEntity, [
            'action' => "companions.php?op=edit&id={$id}",
            'attr' => [
                'autocomplete' => 'off'
            ]
        ]);

        $form->handleRequest();

        if ($form->isSubmitted() && $form->isValid())
        {
            $entity = $form->getData();
            $method = $entity->getCompanionid() ? 'merge' : 'persist';

            \Doctrine::{$method}($entity);
            \Doctrine::flush();

            $id = $entity->getCompanionid();

            \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));

            LotgdCache::removeItem("companiondata-{$id}");

            //-- Redo form for change $id and set new data (generated IDs)
            $form = LotgdForm::create(Lotgd\Core\EntityForm\CompanionsType::class, $entity, [
                'action' => "companions.php?op=edit&id={$id}",
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ]);
        }

        \LotgdNavigation::addNavAllow("companions.php?op=edit&id={$id}");

        $params['form'] = $form->createView();
    }
}

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/companions.twig', $params));

page_footer();
