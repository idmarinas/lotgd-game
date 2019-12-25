<?php

//Author: Lonny Luberts - 3/18/2005
//Heavily modified by JT Traub
require_once 'common.php';

check_su_access(SU_EDIT_USERS);

$textDomain = 'titleedit';

page_header('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain
];

$op = (string) \LotgdHttp::getQuery('op');
$id = (int) \LotgdHttp::getQuery('id');

$editarray = [
    'Titles,title',
    'dk' => 'Dragon Kills,int|0',
    'male' => 'Male Title,text|',
    'female' => 'Female Title,text|',
];

addnav('Other');

\LotgdNavigation::superuserGrottoNav();

$repository = \Doctrine::getRepository('LotgdCore:Titles');

\LotgdNavigation::addHeader('titleedit.category.functions');

if ('save' == $op)
{
    $post = \LotgdHttp::getPostAll();
    $entity = $repository->find($id);
    $entity = $repository->hydrateEntity($post, $entity);

    \Doctrine::persist($entity);

    \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('section.edit.save.success', [], $textDomain));
}
elseif ('delete' == $op)
{
    $entity = $repository->find($id);

    \Doctrine::remove($entity);

    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('section.edit.delete.success', [], $textDomain));
}

\Doctrine::flush();

switch ($op)
{
    case 'reset':

        require_once 'lib/titles.php';
        require_once 'lib/names.php';

        $charRepository = \Doctrine::getRepository('LotgdCore:Characters');
        $characters = $charRepository->findAll();

        foreach ($characters as $row)
        {
            $oname = $row->getName();
            $dk = $row->getDragonkills();
            $otitle = $row->getTitle();
            $dk = $row->getDragonkills();

            if (! valid_dk_title($otitle, $dk, $row->getSex()))
            {
                $sex = translate_inline($row->getSex() ? 'female' : 'male');
                $newtitle = get_dk_title($dk, $row->getSex());
                $newname = change_player_title($newtitle, $row);
                $id = $row->getAcctid();

                if ($oname != $newname)
                {
                    $params['messages'][] = [
                        'section.reset.change.name',
                        [
                            'oldName' => $oname,
                            'newName' => $newname,
                            'newTitle' => $newtitle,
                            'dk' => $dk,
                            'sex' => $row->getSex()
                        ],
                        $textDomain
                    ];

                    if ($session['user']['acctid'] == $row->getAcctid())
                    {
                        $session['user']['title'] = $newtitle;
                        $session['user']['name'] = $newname;
                    }
                }
                elseif ($otitle != $newtitle)
                {
                    $params['messages'][] = [
                        'section.reset.change.title',
                        [
                            'oldName' => $oname,
                            'newTitle' => $newtitle,
                            'dk' => $dk,
                            'sex' => $row->getSex()
                        ],
                        $textDomain
                    ];

                    if ($session['user']['acctid'] == $row->getAcctid())
                    {
                        $session['user']['title'] = $newtitle;
                    }
                }

                $row->setTitle($newtitle)
                    ->setName($newname)
                ;

                \Doctrine::persist($row);
            }
        }

        \Doctrine::flush();

        \LotgdNavigation::addHeader('titleedit.category.functions');
        \LotgdNavigation::addNav('titleedit.nav.main', 'titleedit.php');
    break;

    case 'edit': case 'add':
        $params['tpl'] = 'edit';
        $params['id'] = $id;

        require_once 'lib/showform.php';

        \LotgdNavigation::addHeader('titleedit.category.functions');
        \LotgdNavigation::addNav('titleedit.nav.main', 'titleedit.php');

        $row = ['titleid' => 0, 'male' => '', 'female' => '', 'dk' => 0];

        if ('edit' == $op)
        {
            $result = $repository->extractEntity($repository->find($id));

            $row = array_merge($row, $result);
        }

        $params['form'] = lotgd_showform($editarray, $row, false, false, false);
    break;

    default:
        $params['paginator'] = $repository->findBy([], ['dk' => 'ASC']);

        \LotgdNavigation::addHeader('titleedit.category.functions');
        \LotgdNavigation::addNav('titleedit.nav.add', 'titleedit.php?op=add');
        \LotgdNavigation::addNav('titleedit.nav.refresh', 'titleedit.php');
        \LotgdNavigation::addNav('titleedit.nav.reset', 'titleedit.php?op=reset');
    break;
}

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/titleedit.twig', $params));

page_footer();
