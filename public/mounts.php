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
        $module = (string) \LotgdHttp::getQuery('module');

        $form = module_objpref_edit('mounts', $module, $mountId);

        $params['isLaminas'] = $form instanceof Laminas\Form\Form;
        $params['module'] = $module;
        $params['mountId'] = $mountId;

        if ($params['isLaminas'])
        {
            $form->setAttribute('action', "mounts.php?op=edit&subop=module&id=${mountId}&module=${module}");
            $params['formTypeTab'] = $form->getOption('form_type_tab');
        }

        if (\LotgdHttp::isPost())
        {
            $post = \LotgdHttp::getPostAll();

            if ($params['isLaminas'])
            {
                $form->setData($post);

                if ($form->isValid())
                {
                    $data = $form->getData();

                    process_post_save_data($data, $mountId, $module);

                    \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));
                }
            }
            else
            {
                reset($post);

                process_post_save_data($post, $mountId, $module);

                \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));
            }
        }

        $params['form'] = $form;

        \LotgdNavigation::addNav('', "mounts.php?op=edit&subop=module&id=${mountId}&module=${module}");

        rawoutput(\LotgdTheme::renderLotgdTemplate('core/page/mounts/module.twig', $params));

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

function process_post_save_data($data, $mountId, $module)
{
    foreach ($data as $key => $val)
    {
        if (is_array($val))
        {
            process_post_save_data($val, $mountId, $module);

            continue;
        }

        set_module_objpref('mounts', $mountId, $key, $val, $module);
    }
}

page_footer();
