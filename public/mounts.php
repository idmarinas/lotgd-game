<?php

// addnews ready
// mail ready
// translator ready
require_once 'common.php';

$op = \LotgdHttp::getQuery('op');
$mountId = \LotgdHttp::getQuery('id');

check_su_access(SU_EDIT_MOUNTS);

$textDomain = 'grotto-mounts';

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
    LotgdCache::removeItem("mountdata-$mountId");
}
elseif ('activate' == $op)
{
    $entity = $repository->find($mountId);
    $entity->setMountactive(true);

    \Doctrine::persist($entity);

    $op = '';
    \LotgdHttp::setQuery('op', '');
    LotgdCache::removeItem("mountdata-$mountId");
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
    LotgdCache::removeItem("mountdata-$mountId");
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

    if ('module' == $subop)
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
elseif ('edit' == $op || 'add' == $op)
{
    \LotgdNavigation::addNav('mounts.nav.editor', 'mounts.php');

    $entity = $repository->find($mountId);

    if (! $entity)
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.mount.not.found', [], $textDomain));

        return redirect('mount.php');
    }

    \LotgdNavigation::addNav('mounts.nav.properties', "mounts.php?op=edit&id=$mountId");
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

        $entity = $entity ?: new \Lotgd\Core\Entity\Mounts();
        \Doctrine::detach($entity);

        $form = LotgdForm::create(Lotgd\Core\EntityForm\MountsType::class, $entity, [
            'action' => "mounts.php?op=edit&id={$mountId}",
            'attr' => [
                'autocomplete' => 'off'
            ]
        ]);

        $form->handleRequest();

        if ($form->isSubmitted() && $form->isValid())
        {
            $entity = $form->getData();
            $method = $entity->getMountid() ? 'merge' : 'persist';

            \Doctrine::{$method}($entity);
            \Doctrine::flush();

            $mountId = $entity->getMountid();

            \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));

            //-- Redo form for change $mountId and set new data (generated IDs)
            $form = LotgdForm::create(Lotgd\Core\EntityForm\MountsType::class, $entity, [
                'action' => "mounts.php?op=edit&id={$mountId}",
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ]);

            LotgdCache::removeItem("mountdata-$mountId");
        }

        //-- In this position can updated $mountId var
        \LotgdNavigation::addNavAllow("mounts.php?op=edit&id={$mountId}");

        $params['form'] = $form->createView();
    }
}

rawoutput(\LotgdTheme::renderLotgdTemplate('core/page/mounts.twig', $params));

page_footer();
