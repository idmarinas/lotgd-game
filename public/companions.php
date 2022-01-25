<?php

use Laminas\Form\Form;
use Laminas\Hydrator\ClassMethodsHydrator;
use Lotgd\Core\Entity\Companions;
use Lotgd\Core\EntityForm\CompanionsType;
// addnews ready
// mail ready
// translator ready

// hilarious copy of mounts.php

use Lotgd\Core\Event\Other;

require_once 'common.php';
require_once 'lib/showform.php';

check_su_access(SU_EDIT_MOUNTS);

$repository = Doctrine::getRepository(Companions::class);

$textDomain = 'grotto_companions';
$hydrator   = new ClassMethodsHydrator();

$params = [
    'textDomain' => $textDomain,
];

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

LotgdNavigation::superuserGrottoNav();

LotgdNavigation::addHeader('companions.category.editor');
LotgdNavigation::addNav('companions.nav.add', 'companions.php?op=add');

$op = (string) LotgdRequest::getQuery('op');
$id = (int) LotgdRequest::getQuery('id');

if ('deactivate' == $op)
{
    $companionEntity = $repository->find($id);
    $companionEntity->setCompanionactive(0);

    Doctrine::persist($companionEntity);

    $op = '';
    LotgdRequest::setQuery('op', '');
}
elseif ('activate' == $op)
{
    $companionEntity = $repository->find($id);
    $companionEntity->setCompanionactive(1);

    Doctrine::persist($companionEntity);

    $op = '';
    LotgdRequest::setQuery('op', '');
}
elseif ('del' == $op)
{
    $companionEntity = $repository->find($id);

    Doctrine::remove($companionEntity);

    $op = '';
    LotgdRequest::setQuery('op', '');

    module_delete_objprefs('companions', $id);
}
elseif ('take' == $op)
{
    $companionEntity = $repository->find($id);

    $row = $hydrator->extract($companionEntity);

    if ([] !== $row)
    {
        $row['attack']       += ($row['attackperlevel'] * $session['user']['level']);
        $row['defense']      += ($row['defenseperlevel'] * $session['user']['level']);
        $row['maxhitpoints'] += ($row['maxhitpointsperlevel'] * $session['user']['level']);
        $row['hitpoints'] = $row['maxhitpoints'];

        $row = new Other($row);
        LotgdEventDispatcher::dispatch($row, Other::COMPANION_ALTER);
        $row = modulehook('alter-companion', $row->getData());

        $message       = 'flash.message.take.fail';
        $paramsMessage = [];
        if (LotgdKernel::get('lotgd_core.combat.buffer')->applyCompanion($row['name'], $row))
        {
            $message       = 'flash.message.take.success';
            $paramsMessage = ['name' => $row['name']];
        }

        LotgdFlashMessages::addInfoMessage(LotgdTranslator::t($message, $paramsMessage, $textDomain));
    }

    $op = '';
    LotgdRequest::setQuery('op', '');
}

unset($companionEntity);
Doctrine::flush();

if ('' == $op)
{
    $params['tpl'] = 'default';

    $params['companionsList'] = $repository->getList();
}
elseif ('edit' == $op || 'add' == $op)
{
    $params['tpl'] = 'edit';

    LotgdNavigation::addNav('companions.nav.editor', 'companions.php');
    LotgdNavigation::addNav('companions.nav.properties', "companions.php?op=edit&id={$id}");

    module_editor_navs('prefs-companions', "companions.php?op=edit&subop=module&id={$id}&module=");

    $subop = (string) LotgdRequest::getQuery('subop');

    if ('module' == $subop)
    {
        $module = (string) LotgdRequest::getQuery('module');

        $form = module_objpref_edit('companions', $module, $id);

        $params['isLaminas'] = $form instanceof Form;
        $params['module']    = $module;
        $params['id']        = $id;

        if ($params['isLaminas'])
        {
            $form->setAttribute('action', "companions.php?op=edit&subop=module&id={$id}&module={$module}");
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

                    process_post_save_data($data, $id, $module);

                    LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));
                }
            }
            else
            {
                reset($post);

                process_post_save_data($post, $id, $module);

                LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));
            }
        }

        $params['form'] = $form;

        LotgdNavigation::addNavAllow("companions.php?op=edit&subop=module&id={$id}&module={$module}");

        LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/companions/module.html.twig', $params));

        LotgdResponse::pageEnd();
    }
    else
    {
        $lotgdFormFactory = LotgdKernel::get('form.factory');
        $companionEntity  = $repository->find($id);
        $creatureArray    = $companionEntity ? $repository->extractEntity($companionEntity) : [];
        $companionEntity  = $companionEntity ?: new Companions();

        $form = $lotgdFormFactory->create(CompanionsType::class, $companionEntity, [
            'action' => "companions.php?op=edit&id={$id}",
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

            $id = $entity->getCompanionid();

            LotgdFlashMessages::addInfoMessage(LotgdTranslator::t('flash.message.actions.save.success', [], $textDomain));

            //-- Redo form for change $id and set new data (generated IDs)
            $form = $lotgdFormFactory->create(CompanionsType::class, $entity, [
                'action' => "companions.php?op=edit&id={$id}",
                'attr'   => [
                    'autocomplete' => 'off',
                ],
            ]);
        }
        Doctrine::detach($companionEntity); //-- Avoid Doctrine save a invalid Form

        LotgdNavigation::addNavAllow("companions.php?op=edit&id={$id}");

        $params['form'] = $form->createView();
    }
}

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/companions.html.twig', $params));

function process_post_save_data($data, $id, $module)
{
    foreach ($data as $key => $val)
    {
        if (\is_array($val))
        {
            process_post_save_data($val, $id, $module);

            continue;
        }

        set_module_objpref('companions', $id, $key, $val, $module);
    }
}

//-- Finalize page
LotgdResponse::pageEnd();
