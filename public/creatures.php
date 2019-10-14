<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/listfiles.php';
require_once 'lib/creaturefunctions.php';
require_once 'lib/showform.php';

check_su_access(SU_EDIT_CREATURES);

$textDomain = 'page-creatures';

tlschema('creatures');

page_header('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();

$op = (string) \LotgdHttp::getQuery('op');
$subop = (string) \LotgdHttp::getQuery('subop');
$page = (int) \LotgdHttp::getQuery('page');
$module = (string) \LotgdHttp::getQuery('module');
$creatureId =  ((int) \LotgdHttp::getPost('creatureid') ?: (int) \LotgdHttp::getQuery('creatureid'));

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Creatures::class);
$params = [];

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
    else
    {
        $message = 'flash.message.save.saved';
        $post['createdby'] = $session['user']['login'];

        $creatureEntity = $repository->find($creatureId);
        $creatureEntity = $repository->hydrateEntity($post, $creatureEntity);

        if ($post['notes'] ?? false)
        {
            \LotgdFlashMessages::addInfoMessage($post['notes']);
        }

        \Doctrine::persist($creatureEntity);
        \Doctrine::flush();

        $creatureId = $creatureEntity->getCreatureid();
    }

    if ($message)
    {
        \LotgdFlashMessages::addInfoMessage($message, $paramsFlashMessage, $textDomain);
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
    \LotgdNavigation::addNav('creatures.nav.properties', "creatures.php?op=edit&creatureid=$creatureId");
    \LotgdNavigation::addHeader('creatures.category.add');
    \LotgdNavigation::addNav('creatures.nav.add.other', 'creatures.php?op=add');

    module_editor_navs('prefs-creatures', "creatures.php?op=edit&subop=module&creatureid=$creatureId&module=");

    \LotgdNavigation::addNav('common.category.navigation');
    \LotgdNavigation::addNav('creatures.nav.home', "creatures.php");

    if ('module' == $subop)
    {
        rawoutput("<form action='creatures.php?op=save&subop=module&creatureid=$creatureId&module=$module' method='POST'>");
        module_objpref_edit('creatures', $module, $creatureId);
        rawoutput('</form>');
        addnav('', "creatures.php?op=save&subop=module&creatureid=$creatureId&module=$module");

        page_footer();
    }
    else
    {
        $creatureEntity = $repository->find($creatureId);
        $creatureArray = $creatureEntity ? $repository->extractEntity($creatureEntity) : [];

        //get available scripts
        $sort = list_files('creatureai', []);
        sort($sort);
        $scriptenum = implode('', $sort);
        $scriptenum = ',,none'.$scriptenum;

        $form = [
            'Creature Properties,title',
            'creatureid' => 'Creature id,hidden',
            'creaturename' => 'Creature Name',
            'creaturecategory' => 'Creature Category',
            'creatureimage' => 'Creature image',
            'creaturedescription' => 'Creature description,textarea',
            'creatureweapon' => 'Weapon',
            'creaturegoldbonus' => 'Gold multiplier,float|0',
            'It is a multiplier that affects the basis of the attribute for the gold that the creature carries; between 0 and 99.99,note',
            'creaturedefensebonus' => 'Defense multiplier,float|1',
            'It is a multiplier that affects the basis of the attribute for the defense that the creature has; between 0 and 99.99,note',
            'creatureattackbonus' => 'Attack multiplier,float|1',
            'It is a multiplier that affects the basis of the attribute for the attack that the creature has; between 0 and 99.99,note',
            'creaturehealthbonus' => 'Health multiplier,float|1',
            'It is a multiplier that affects the basis of the attribute for the health that the creature has; between 0 and 99.99,note',
            'creaturewin' => 'Win Message',
            'creaturelose' => 'Death Message',
            'forest' => 'Creature is in forest?,bool',
            'graveyard' => 'Creature is in graveyard?,bool',
            'creatureaiscript' => "Creature's A.I.,enum".$scriptenum,
        ];

        $params['form'] = lotgd_showform($form, $creatureArray, false, false, false);
        $params['creature'] = $creatureArray;
    }
}

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/creatures.twig', $params));

page_footer();
