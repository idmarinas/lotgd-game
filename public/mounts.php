<?php

use Laminas\Form\Form;
use Lotgd\Core\Entity\Mounts;
use Lotgd\Core\EntityForm\MountsType;

// addnews ready
// mail ready
// translator ready
require_once 'common.php';

$op      = LotgdRequest::getQuery('op');
$mountId = LotgdRequest::getQuery('id');

check_su_access(SU_EDIT_MOUNTS);

$textDomain = 'grotto_mounts';

$params = [
    'textDomain' => $textDomain,
];

$repository = Doctrine::getRepository('LotgdCore:Mounts');

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

LotgdNavigation::superuserGrottoNav();

LotgdNavigation::addHeader('mounts.category.editor');
LotgdNavigation::addNav('mounts.nav.add', 'mounts.php?op=add');

if ('deactivate' == $op)
{
    $entity = $repository->find($mountId);
    $entity->setMountactive(false);

    Doctrine::persist($entity);

    $op = '';
    LotgdRequest::setQuery('op', '');
}
elseif ('activate' == $op)
{
    $entity = $repository->find($mountId);
    $entity->setMountactive(true);

    Doctrine::persist($entity);

    $op = '';
    LotgdRequest::setQuery('op', '');
}
elseif ('del' == $op)
{
    //refund for anyone who has a mount of this type.
    $entity = $repository->find($mountId);
    $repository->refundMount($entity);

    //drop the mount.
    Doctrine::remove($entity);

    module_delete_objprefs('mounts', $mountId);

    $op = '';
    LotgdRequest::setQuery('op', '');
}
elseif ('give' == $op)
{
    $session['user']['hashorse'] = $mountId;

    $entity = $repository->find($mountId);
    $buff   = $entity->getMountbuff();

    $buff['schema'] = $buff['schema'] ?: 'mounts';

    LotgdKernel::get('lotgd_core.combat.buffer')->applyBuff('mount', $buff);

    $op = '';
    LotgdRequest::setQuery('op', '');
}

Doctrine::flush();

if ('' == $op)
{
    $params['tpl'] = 'default';

    $params['mounts'] = $repository->getList();
}
elseif ('edit' == $op || 'add' == $op)
{
    LotgdNavigation::addNav('mounts.nav.editor', 'mounts.php');

    $entity = $repository->find($mountId);

    if ( ! $entity)
    {
        LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('flash.message.mount.not.found', [], $textDomain));

        redirect('mount.php');
    }

    LotgdNavigation::addNav('mounts.nav.properties', "mounts.php?op=edit&id={$mountId}");
    module_editor_navs('prefs-mounts', "mounts.php?op=edit&subop=module&id={$mountId}&module=");
    $subop = LotgdRequest::getQuery('subop');

    if ('module' == $subop)
    {
        $module = (string) LotgdRequest::getQuery('module');

        $form = module_objpref_edit('mounts', $module, $mountId);

        $params['isLaminas'] = $form instanceof Form;
        $params['module']    = $module;
        $params['mountId']   = $mountId;

        if ($params['isLaminas'])
        {
            $form->setAttribute('action', "mounts.php?op=edit&subop=module&id={$mountId}&module={$module}");
            $params['formTypeTab'] = $form->getOption('form_type_tab');
        }

        if (LotgdRequest::isPost())
        {
            $post = LotgdRequest::getPostAll();

            if ($params['isLaminas'])
            {
                $form->setData($post);

                if ($form->isValid())
                {
                    $data = $form->getData();

                    process_post_save_data($data, $mountId, $module);

                    LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));
                }
            }
            else
            {
                reset($post);

                process_post_save_data($post, $mountId, $module);

                LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));
            }
        }

        $params['form'] = $form;

        LotgdNavigation::addNav('', "mounts.php?op=edit&subop=module&id={$mountId}&module={$module}");

        LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/mounts/module.html.twig', $params));

        LotgdResponse::pageEnd();
    }
    else
    {
        $params['tpl'] = 'edit';

        $lotgdFormFactory = LotgdKernel::get('form.factory');
        $entity           = $entity ?: new Mounts();

        $form = $lotgdFormFactory->create(MountsType::class, $entity, [
            'action' => "mounts.php?op=edit&id={$mountId}",
            'attr'   => [
                'autocomplete' => 'off',
            ],
        ]);

        $form->handleRequest(LotgdRequest::_i());

        if ($form->isSubmitted() && $form->isValid())
        {
            $entity = $form->getData();

            Doctrine::persist($entity);
            Doctrine::flush();

            $mountId = $entity->getMountid();

            LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));

            //-- Redo form for change $mountId and set new data (generated IDs)
            $form = $lotgdFormFactory->create(MountsType::class, $entity, [
                'action' => "mounts.php?op=edit&id={$mountId}",
                'attr'   => [
                    'autocomplete' => 'off',
                ],
            ]);
        }
        Doctrine::detach($entity); //-- Avoid Doctrine save a invalid Form

        //-- In this position can updated $mountId var
        LotgdNavigation::addNavAllow("mounts.php?op=edit&id={$mountId}");

        $params['form'] = $form->createView();
    }
}

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/mounts.html.twig', $params));

function process_post_save_data($data, $mountId, $module)
{
    foreach ($data as $key => $val)
    {
        if (\is_array($val))
        {
            process_post_save_data($val, $mountId, $module);

            continue;
        }

        set_module_objpref('mounts', $mountId, $key, $val, $module);
    }
}

//-- Finalize page
LotgdResponse::pageEnd();
