<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/showform.php';

check_su_access(SU_EDIT_CREATURES);

$textDomain = 'grotto_creatures';

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();

$op = (string) \LotgdRequest::getQuery('op');
$subop = (string) \LotgdRequest::getQuery('subop');
$page = (int) \LotgdRequest::getQuery('page');
$module = (string) \LotgdRequest::getQuery('module');
$creatureId = ((int) \LotgdRequest::getPost('creatureid') ?: (int) \LotgdRequest::getQuery('creatureid'));

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Creatures::class);
$params = [
    'textDomain' => $textDomain
];

if ('del' == $op)
{
    $creatureEntity = $repository->find($creatureId);

    $message = 'flash.message.del.error';
    $messageType = 'addErrorMessage';

    if ($creatureEntity)
    {
        \Doctrine::remove($creatureEntity);
        \Doctrine::flush();

        $message = 'flash.message.del.success';
        $messageType = 'addSuccessMessage';
        module_delete_objprefs('creatures', $creatureId);
    }

    \LotgdFlashMessages::{$messageType}(\LotgdTranslator::t($message, ['name' => $creatureEntity->getCreaturename()], $textDomain));

    unset($creatureEntity);
    $op = '';
}

if ('' == $op || 'search' == $op)
{
    $params['tpl'] = 'default';

    \LotgdNavigation::addHeader('creatures.category.edit');
    \LotgdNavigation::addNav('creatures.nav.add', 'creatures.php?op=add');

    $q = trim(\LotgdRequest::getPost('q'));

    $query = $repository->createQueryBuilder('u');

    if ($q !== '' && $q !== '0')
    {
        $query->where('u.creaturename LIKE :q')
            ->orWhere('u.creaturecategory LIKE :q')
            ->orWhere('u.creatureweapon LIKE :q')
            ->orWhere('u.creaturelose LIKE :q')
            ->orWhere('u.createdby LIKE :q')
            ->setParameter('q', "%$q%")
        ;
    }

    $params['paginator'] = $repository->getPaginator($query, $page);
}
elseif ('edit' == $op || 'add' == $op)
{
    $params['tpl'] = 'edit';

    \LotgdNavigation::addHeader('creatures.category.edit');
    \LotgdNavigation::addHeader('creatures.category.add');

    module_editor_navs('prefs-creatures', "creatures.php?op=edit&subop=module&creatureid=$creatureId&module=");

    \LotgdNavigation::addNav('common.category.navigation');
    \LotgdNavigation::addNav('creatures.nav.home', 'creatures.php');

    if ('module' == $subop)
    {
        $form = module_objpref_edit('creatures', $module, $creatureId);

        $params['isLaminas'] = $form instanceof Laminas\Form\Form;
        $params['module'] = $module;
        $params['creatureId'] = $creatureId;

        if ($params['isLaminas'])
        {
            $form->setAttribute('action', "companions.php?op=edit&subop=module&creatureid={$id}&module={$module}");
            $params['formTypeTab'] = $form->getOption('form_type_tab');
        }

        if (\LotgdRequest::isPost())
        {
            $post = \LotgdRequest::getPostAll();
            $paramsFlashMessages = ['name' => $module];

            if ($params['isLaminas'])
            {
                $form->setData($post);

                if ($form->isValid())
                {
                    $data = $form->getData();

                    process_post_save_data($data, $creatureId, $module);

                    \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.save.module', $paramsFlashMessage, $textDomain));
                }
            }
            else
            {
                reset($post);

                process_post_save_data($post, $creatureId, $module);

                \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.save.module', $paramsFlashMessage, $textDomain));
            }
        }

        $params['form'] = $form;

        \LotgdNavigation::addNavAllow("creatures.php?op=save&subop=module&creatureid={$creatureId}&module={$module}");

        \LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/creatures/module.html.twig', $params));

        \LotgdResponse::pageEnd();
    }
    else
    {
        $lotgdFormFactory = \LotgdKernel::get('form.factory');
        $creatureEntity = $repository->find($creatureId);
        $creatureArray = $creatureEntity ? $repository->extractEntity($creatureEntity) : [];
        $creatureEntity = $creatureEntity ?: new \Lotgd\Core\Entity\Creatures();

        $form = $lotgdFormFactory->create(Lotgd\Core\EntityForm\CreaturesType::class, $creatureEntity, [
            'action' => "creatures.php?op=edit&creatureid={$creatureId}",
            'attr' => [
                'autocomplete' => 'off'
            ]
        ]);

        $form->handleRequest(\LotgdRequest::_i());

        if ($form->isSubmitted() && $form->isValid())
        {
            $entity = $form->getData();

            \Doctrine::persist($entity);
            \Doctrine::flush();

            $creatureId = $entity->getCreatureid();

            \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.save.saved', [], $textDomain));

            //-- Redo form for change $creatureId and set new data (generated IDs)
            $form = $lotgdFormFactory->create(Lotgd\Core\EntityForm\CreaturesType::class, $entity, [
                'action' => "creatures.php?op=edit&creatureid={$creatureId}",
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ]);
        }
        \Doctrine::detach($creatureEntity); //-- Avoid Doctrine save a invalid Form

        //-- In this position can updated $creatureId var
        \LotgdNavigation::addHeader('creatures.category.edit');
        \LotgdNavigation::addNav('creatures.nav.properties', "creatures.php?op=edit&creatureid=$creatureId");
        \LotgdNavigation::addHeader('creatures.category.add');
        \LotgdNavigation::addNav('creatures.nav.add.other', 'creatures.php?op=add');
        \LotgdNavigation::addNavAllow("creatures.php?op=edit&creatureid={$creatureId}");

        $params['form'] = $form->createView();
        $params['creature'] = $creatureArray;
    }
}

\LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/creatures.html.twig', $params));

function process_post_save_data($data, $creatureId, $module)
{
    foreach ($data as $key => $val)
    {
        if (is_array($val))
        {
            process_post_save_data($val, $creatureId, $module);

            continue;
        }

        set_module_objpref('creatures', $creatureId, $key, $val, $module);
    }
}

//-- Finalize page
\LotgdResponse::pageEnd();
