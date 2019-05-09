<?php

// addnews ready
// mail ready
// translator ready

// hilarious copy of mounts.php
require_once 'common.php';
require_once 'lib/showform.php';

check_su_access(SU_EDIT_MOUNTS);

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Companions::class);

$textDomain = 'page-companions';
$hydrator = new \Zend\Hydrator\ClassMethods();

$params = [];

tlschema('companions');

page_header('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();

\LotgdNavigation::addHeader('companions.category.editor');
\LotgdNavigation::addNav('companions.nav.add', 'companions.php?op=add');

$op = (string) \LotgdHttp::getQuery('op');
$id = (int) \LotgdHttp::getQuery('id');

$vname = getsetting('villagename', LOCATION_FIELDS);
$locs = [
    $vname => [
        'location.village.of',
        ['name' => $vname],
        'app-default'
    ]
];
$locs = modulehook('camplocs', $locs);
$locs['all'] = [
    'location.everywhere',
    [],
    'app-default'
];
ksort($locs);
reset($locs);

$params['locations'] = $locs;

if ('deactivate' == $op)
{
    $companionEntity = $repository->find($id);
    $companionEntity->setCompanionactive(0);

    \Doctrine::persist($companionEntity);

    $op = '';
    \LotgdHttp::setQuery('op', '');

    invalidatedatacache("companionsdata-{$id}");
}
elseif ('activate' == $op)
{
    $companionEntity = $repository->find($id);
    $companionEntity->setCompanionactive(1);

    \Doctrine::persist($companionEntity);

    $op = '';
    \LotgdHttp::setQuery('op', '');

    invalidatedatacache("companiondata-{$id}");
}
elseif ('del' == $op)
{
    $companionEntity = $repository->find($id);

    \Doctrine::remove($companionEntity);

    $op = '';
    \LotgdHttp::setQuery('op', '');

    module_delete_objprefs('companions', $id);
    invalidatedatacache("companiondata-$id");
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

    if ('' == $subop)
    {
        $companion = \LotgdHttp::getPost('companion');

        if ($companion)
        {
            $companion['allowinshades'] = $companion['allowinshades'] ?? 0;
            $companion['allowinpvp'] = $companion['allowinpvp'] ?? 0;
            $companion['allowintrain'] = $companion['allowintrain'] ?? 0;
            $companion['abilities']['fight'] = (bool) ($companion['abilities']['fight'] ?? false);
            $companion['abilities']['defend'] = (bool) ($companion['abilities']['defend'] ?? false);
            $companion['cannotdie'] = (bool) ($companion['cannotdie'] ?? false);
            $companion['cannotbehealed'] = (bool) ($companion['cannotbehealed'] ?? false);

            $companionEntity = $repository->find($id);

            if (! $companionEntity)
            {
                $companionEntity = new \Lotgd\Core\Entity\Companions();
            }

            $companionEntity = $hydrator->hydrate($companion, $companionEntity);

            \Doctrine::persist($companionEntity);

            invalidatedatacache("companiondata-{$id}");

            \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));
        }
    }
    elseif ('module' == $subop)
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
elseif ('add' == $op)
{
    $params['tpl'] = 'edit';
    $params['companion'] = [];

    \LotgdNavigation::addNav('companions.nav.editor', 'companions.php');
}
elseif ('edit' == $op)
{
    \LotgdNavigation::addNav('companions.nav.editor', 'companions.php');

    $companionEntity = $repository->find($id);

    if (! $companionEntity)
    {
        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('', [], $textDomain));

        return redirect('companions.php');
    }

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
        $params['tpl'] = 'edit';
        $params['companion'] = $companionEntity;
    }
}

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/companions.twig', $params));

page_footer();
