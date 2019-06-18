<?php

// addnews ready
// mail ready
// translator ready
require_once 'common.php';

$op = \LotgdHttp::getQuery('op');
$mountId = \LotgdHttp::getQuery('id');

check_su_access(SU_EDIT_MOUNTS);

$textDomain = 'page-mounts';

tlschema('mounts');

$params = [
    'textDomain' => $textDomain
];

$repository = \Doctrine::getRepository('LotgdCore:Mounts');

page_header('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();

\LotgdNavigation::addHeader('mounts.category.editor');
\LotgdNavigation::addNav('mounts.nav.add', 'mounts.php?op=add');

if ('deactivate' == $op)
{
    $entity = $repository->find($mountId);
    $entity->setMountactive(false);

    \Doctrine::persist($entity);

    $op = '';
    \LotgdHttp::setQuery('op', '');
    invalidatedatacache("mountdata-$mountId");
}
elseif ('activate' == $op)
{
    $entity = $repository->find($mountId);
    $entity->setMountactive(true);

    \Doctrine::persist($entity);

    $op = '';
    \LotgdHttp::setQuery('op', '');
    invalidatedatacache("mountdata-$mountId");
}
elseif ('del' == $op)
{
    //refund for anyone who has a mount of this type.
    $entity = $repository->find($mountId);
    $repository->refundMount($entity);

    //drop the mount.
    \Doctrine::remove($entity);

    module_delete_objprefs('mounts', $mountId);

    $op = '';
    \LotgdHttp::setQuery('op', '');
    invalidatedatacache("mountdata-$mountId");
}
elseif ('give' == $op)
{
    $session['user']['hashorse'] = $mountId;

    $entity = $repository->find($mountId);
    $buff = $entity->getMountbuff();

    $buff['schema'] = $buff['schema'] ?: 'mounts';

    apply_buff('mount', $buff);

    $op = '';
    \LotgdHttp::setQuery('op', '');
}
elseif ('save' == $op)
{
    $subop = (string) \LotgdHttp::getQuery('subop');

    if ('' == $subop)
    {
        $buff = [];
        $mount = LotgdHttp::getPost('mount');

        if ($mount)
        {
            $mount['mountbuff']['schema'] = 'mounts';

            $mountEntity = $repository->find($mountId);
            $mountEntity = $repository->hydrateEntity($mount, $mountEntity);

            \Doctrine::persist($mountEntity);

            invalidatedatacache("mountdata-$mountId");

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
            set_module_objpref('mounts', $mountId, $key, $val, $module);
        }

        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));
    }

    $op = $mountId ? 'edit' : '';

    \LotgdHttp::setQuery('op', $op);
}

\Doctrine::flush();

if ('' == $op)
{
    $params['tpl'] = 'default';

    $params['mounts'] = $repository->getList();
}
elseif ('add' == $op)
{
    \LotgdNavigation::addNav('mounts.nav.editor', 'mounts.php');

    $params['tpl'] = 'edit';
    $params['mount'] = [];

    // Run a modulehook to find out where stables are located.  By default
    // they are located in 'Degolburg' (ie, getgamesetting('villagename'));
    // Some later module can remove them however.
    $vname = getsetting('villagename', LOCATION_FIELDS);
    $locs = modulehook('stablelocs', [
        $vname => \LotgdTranslator::t('section.edit.form.select.option.village', [ 'village' => $vname ], $textDomain)
    ]);
    $locs['all'] = \LotgdTranslator::t('section.edit.form.select.option.all', [], $textDomain);

    ksort($locs);
    reset($locs);

    $params['stableLocs'] = $locs;
}
elseif ('edit' == $op)
{
    \LotgdNavigation::addNav('mounts.nav.editor', 'mounts.php');

    $entity = $repository->find($mountId);

    if (! $entity)
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.mount.not.found', [], $textDomain));

        return redirect('mount.php');
    }

    \LotgdNavigation::addNav('mounts.nav.mount.properties', "mounts.php?op=edit&id=$mountId");
    module_editor_navs('prefs-mounts', "mounts.php?op=edit&subop=module&id=$mountId&module=");
    $subop = \LotgdHttp::getQuery('subop');

    if ('module' == $subop)
    {
        $module = \LotgdHttp::getQuery('module');
        rawoutput("<form action='mounts.php?op=save&subop=module&id=$mountId&module=$module' method='POST'>");
        module_objpref_edit('mounts', $module, $mountId);
        rawoutput('</form>');
        \LotgdNavigation::addNav('', "mounts.php?op=save&subop=module&id=$mountId&module=$module");

        page_footer();
    }
    else
    {
        $params['tpl'] = 'edit';
        $params['mount'] = $repository->extractEntity($entity);
    }
}

rawoutput(\LotgdTheme::renderLotgdTemplate('core/page/mounts.twig', $params));

page_footer();
