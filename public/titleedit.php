<?php

//Author: Lonny Luberts - 3/18/2005
//Heavily modified by JT Traub
require_once 'common.php';

check_su_access(SU_EDIT_USERS);

$textDomain = 'grotto_titleedit';

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain
];

$op = (string) \LotgdRequest::getQuery('op');
$id = (int) \LotgdRequest::getQuery('id');

\LotgdNavigation::addNav('titleedit.category.other');

\LotgdNavigation::superuserGrottoNav();

$repository = \Doctrine::getRepository('LotgdCore:Titles');

\LotgdNavigation::addHeader('titleedit.category.functions');

if ('delete' == $op)
{
    $entity = $repository->find($id);

    \Doctrine::remove($entity);

    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('section.edit.delete.success', [], $textDomain));
}

\Doctrine::flush();

switch ($op)
{
    case 'reset':
        $charRepository = \Doctrine::getRepository('LotgdCore:Avatar');
        $characters = $charRepository->findAll();

        foreach ($characters as $row)
        {
            $oname = $row->getName();
            $dk = $row->getDragonkills();
            $otitle = $row->getTitle();
            $dk = $row->getDragonkills();

            if (! LotgdTool::validDkTitle($otitle, $dk, $row->getSex()))
            {
                $newtitle = LotgdTool::getDkTitle($dk, $row->getSex());
                $newname = LotgdTool::changePlayerTitle($newtitle, $row);
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

        $lotgdFormFactory = \LotgdKernel::get('form.factory');
        $entity = $repository->find($id);
        $entity = $entity ?: new \Lotgd\Core\Entity\Titles();

        $form = $lotgdFormFactory->create(Lotgd\Core\EntityForm\TitlesType::class, $entity, [
            'action' => "titleedit.php?op=edit&id={$id}",
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

            $id = $entity->getTitleid();

            \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('section.edit.save.success', [], $textDomain));

            //-- Redo form for change $id and set new data (generated IDs)
            $form = $lotgdFormFactory->create(Lotgd\Core\EntityForm\TitlesType::class, $entity, [
                'action' => "titleedit.php?op=edit&id={$id}",
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ]);
        }
        \Doctrine::detach($entity); //-- Avoid Doctrine save a invalid Form

        //-- In this position can updated $id var
        \LotgdNavigation::addNavAllow("titleedit.php?op=edit&id={$id}");

        \LotgdNavigation::addHeader('titleedit.category.functions');
        \LotgdNavigation::addNav('titleedit.nav.main', 'titleedit.php');

        $params['form'] = $form->createView();
    break;

    default:
        $params['paginator'] = $repository->getList();

        \LotgdNavigation::addHeader('titleedit.category.functions');
        \LotgdNavigation::addNav('titleedit.nav.add', 'titleedit.php?op=add');
        \LotgdNavigation::addNav('titleedit.nav.refresh', 'titleedit.php');
        \LotgdNavigation::addNav('titleedit.nav.reset', 'titleedit.php?op=reset');
    break;
}

\LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/titleedit.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
