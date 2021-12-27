<?php

/**
 * This file is part of "LoTGD Core Package".
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 7.0.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Core\Entity\Motd;
use Lotgd\Core\Entity\Pollresults;
use Lotgd\Core\EntityForm\MotdEditType;
use Lotgd\Core\EntityForm\MotdPollType;
use Lotgd\Core\EntityForm\MotdType;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Pattern\LotgdControllerTrait;
use Lotgd\Core\Repository\MotdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tracy\Debugger;

class MotdController extends AbstractController
{
    use LotgdControllerTrait;

    public const TRANSLATION_DOMAIN = 'jaxon_motd';

    private $translator;
    private $repository;

    public function __construct(TranslatorInterface $translator, MotdRepository $repository)
    {
        $this->translator = $translator;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $qb           = $this->repository->createQueryBuilder('u');
        $params       = $this->getParams();
        $page         = max(1, $request->query->getInt('page', 1));
        $itemsPerPage = max(5, $request->query->getInt('items_per_page', 1));
        $month        = (string) $request->query->get('month', '');

        // Dialog content
        try
        {
            $qb->select('u', 'c.name as motdauthorname')
                ->leftJoin('LotgdCore:User', 'a', 'with', $qb->expr()->eq('a.acctid', 'u.motdauthor'))
                ->leftJoin('LotgdCore:Avatar', 'c', 'with', $qb->expr()->eq('c.id', 'a.avatar'))
                ->orderBy('u.motddate', 'DESC')
            ;

            if ($month)
            {
                $params['month_selected'] = $month;

                $month = explode('-', $month);
                $qb->where('MONTH(u.motddate) = :month AND YEAR(u.motddate) = :year')
                    ->setParameters([
                        'month' => $month[1],
                        'year'  => $month[0],
                    ])
                ;
            }

            $params['pagination'] = $this->repository->getPaginator($qb, $page, $itemsPerPage);
            //-- // $params['motd_moth_count_per_year'] = $this->repository->getMonthCountPerYear();

            $session['needtoviewmotd'] = false;

            $lastMotd = $this->repository->getLastMotdDate();

            if (null !== $lastMotd)
            {
                $session['user']['lastmotd'] = $lastMotd;
            }
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $this->addNotification('error', $this->translator->trans('list.fail', [], self::TRANSLATION_DOMAIN));
        }

        return $this->render('motd/list.html.twig', $params);
    }

    public function item(Request $request): Response
    {
        global $session;

        if ( ! checkSuPermission(SU_POST_MOTD))
        {
            return new Response('', 403);
        }

        $itemId = $request->query->getInt('id');
        $params = $this->getParams();

        $entity = null;

        $formType = MotdType::class;

        $params['is_edit'] = false;

        if ($itemId > 0)
        {
            $formType = MotdEditType::class;
            $entity   = $this->repository->find($itemId);
            $params['is_edit'] = true;

            $params['motdData'] = $this->repository->getEditMotdItem($itemId);
        }

        $entity = $entity ?: new Motd();

        $form = $this->createForm($formType, $entity, [
            'attr' => [
                'autocomplete' => 'off',
            ],
        ]);
        $formEmpty = clone $form;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entity->setMotdtype(false);

            if ($params['is_edit'])
            {
                if ($form->get('changedate')->getData())
                {
                    $entity->setMotddate(new \DateTime('now'));
                }

                if ($form->get('changeauthor')->getData())
                {
                    $entity->setMotdauthor($session['user']['acctid']);
                }
            }
            else
            {
                $form = $formEmpty;
                $entity->setMotdauthor($session['user']['acctid']);
            }

            $this->getDoctrine()->getManager()->persist($entity);
            $this->getDoctrine()->getManager()->flush();

            $this->addNotification('success', $this->translator->trans('item.add.item.success', [], self::TRANSLATION_DOMAIN));

            $isSubmitted = true;
        }

        $params['form']    = $form->createView();

        $isSubmitted = $isSubmitted ?? $form->isSubmitted();

        return $this->renderBlock('motd/edit_item.html.twig', $isSubmitted ? 'content' : 'dialog', array_merge($params, [
            'is_post' => $isSubmitted,
            'form'    => $form->createView(),
        ]));
    }

    public function poll(Request $request): Response
    {
        global $session;

        if ( ! checkSuPermission(SU_POST_MOTD))
        {
            return new Response('', 403);
        }

        $params = $this->getParams();

        $form = $this->createForm(MotdPollType::class, new Motd(), [
            'action' => '',
            'attr'   => [
                'autocomplete' => 'off',
            ],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entity = $form->getData();

            $choices = $form->get('opt')->getData();
            $body    = \serialize(['body' => $entity->getMotdbody(), 'opt' => $choices]);

            $entity->setMotdbody($body);
            $entity->setMotdtype(true);
            $entity->setMotdauthor($session['user']['acctid']);

            $this->getDoctrine()->getManager()->persist($entity);
            $this->getDoctrine()->getManager()->flush();

            $this->addNotification('success', $this->translator->trans('item.add.poll.success', [], self::TRANSLATION_DOMAIN));

            return $this->index($request);
        }

        $params['form'] = $form->createView();

        return $this->render('motd/add_poll.html.twig', $params);
    }

    public function delete(Request $request): Response
    {
        if ( ! checkSuPermission(SU_POST_MOTD))
        {
            return new Response('', 403);
        }

        $id = $request->query->getInt('id');

        $entity = $this->repository->find($id);

        $message = $this->translator->trans('item.del.not.found', [], self::TRANSLATION_DOMAIN);
        $type    = 'warning';

        if ($entity)
        {
            $this->getDoctrine()->getManager()->remove($entity);
            $this->getDoctrine()->getManager()->flush();

            $message = $this->translator->trans('item.del.deleted', ['id' => $id], self::TRANSLATION_DOMAIN);
            $type    = 'success';
        }

        $this->addNotification($type, $message);

        return $this->index($request);
    }

    /**
     * Vote in a poll.
     */
    public function vote(Request $request): Response
    {
        global $session;

        $pollId = $request->query->getInt('item_id');
        $choice = $request->query->getInt('option_id');

        //-- Do nothing if there is no active session
        if ( ! ($session['user']['loggedin'] ?? false))
        {
            return $this->index($request);
        }

        try
        {
            $pollResult = new Pollresults();

            $pollResult->setChoice($choice)
                ->setAccount($session['user']['acctid'])
                ->setMotditem($pollId)
            ;

            $this->getDoctrine()->getManager()->persist($pollResult);
            $this->getDoctrine()->getManager()->flush();

            $type    = 'success';
            $message = $this->translator->trans('item.poll.voting.success', [], self::TRANSLATION_DOMAIN);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $type    = 'error';
            $message = $this->translator->trans('item.poll.voting.fail', [], self::TRANSLATION_DOMAIN);
        }

        $this->addNotification($type, $message);

        return $this->index($request);
    }

    /**
     * Get default params.
     */
    private function getParams(): array
    {
        global $session;

        return [
            'translation_domain' => self::TRANSLATION_DOMAIN,
            'SU_POST_MOTD'       => ($session['user']['superuser'] & SU_POST_MOTD),
        ];
    }
}
