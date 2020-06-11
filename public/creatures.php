<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/listfiles.php';
require_once 'lib/creaturefunctions.php';
require_once 'lib/showform.php';

check_su_access(SU_EDIT_CREATURES);

$textDomain = 'grotto-creatures';

page_header('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();

$op = (string) \LotgdHttp::getQuery('op');
$subop = (string) \LotgdHttp::getQuery('subop');
$page = (int) \LotgdHttp::getQuery('page');
$module = (string) \LotgdHttp::getQuery('module');
$creatureId = ((int) \LotgdHttp::getPost('creatureid') ?: (int) \LotgdHttp::getQuery('creatureid'));

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Creatures::class);
$params = [
    'textDomain' => $textDomain
];

if ('save' == $op)
{
    $post = \LotgdHttp::getPostAll();

    $message = '';
    $paramsFlashMessage = [];

    if ('module' == $subop)
    {
        $message = 'flash.message.save.module';
        $paramsFlashMessages = ['name' => $module];
        // Save module settings
        reset($post);

        foreach ($post as $key => $val)
        {
            set_module_objpref('creatures', $creatureId, $key, $val, $module);
        }
    }

    if ($message)
    {
        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t($message, $paramsFlashMessage, $textDomain));
    }

    $op = 'edit';
    unset($message, $creatureEntity, $post);
}
elseif ('del' == $op)
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

    $q = (string) trim(\LotgdHttp::getPost('q'));

    $query = $repository->createQueryBuilder('u');

    if ($q)
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
        rawoutput("<form action='creatures.php?op=save&subop=module&creatureid=$creatureId&module=$module' method='POST'>");
        module_objpref_edit('creatures', $module, $creatureId);
        rawoutput('</form>');
        \LotgdNavigation::addNavAllow("creatures.php?op=save&subop=module&creatureid=$creatureId&module=$module");

        page_footer();
    }
    else
    {
        $creatureEntity = $repository->find($creatureId);
        $creatureArray = $creatureEntity ? $repository->extractEntity($creatureEntity) : [];
        $creatureEntity = $creatureEntit ?: new \Lotgd\Core\Entity\Creatures();
        \Doctrine::detach($creatureEntity);

        $form = LotgdForm::create(Lotgd\Core\EntityForm\CreaturesType::class, $creatureEntity, [
            'action' => "creatures.php?op=edit&creatureid={$creatureId}",
            'attr' => [
                'autocomplete' => 'off'
            ]
        ]);

        $form->handleRequest();

        if ($form->isSubmitted() && $form->isValid())
        {
            $entity = $form->getData();
            $method = $entity->getCreatureid() ? 'merge' : 'persist';

            \Doctrine::{$method}($entity);
            \Doctrine::flush();

            $creatureId = $entity->getCreatureid();

            \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.save.saved', [], $textDomain));

            //-- Redo form for change $creatureId and set new data (generated IDs)
            $form = LotgdForm::create(Lotgd\Core\EntityForm\CreaturesType::class, $entity, [
                'action' => "creatures.php?op=edit&creatureid={$creatureId}",
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ]);
        }

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

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/creatures.twig', $params));

page_footer();
