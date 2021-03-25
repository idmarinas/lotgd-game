<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/list")
 */
class ListController extends AbstractController
{
    public const TRANSLATOR_DOMAIN = 'lotgd_core_page_list';

    /**
     * @Route("/warriors", name="lotgd_core_page_list")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        /** @var º\Doctrine\ORM\EntityManagerInterface */
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("SELECT u FROM LotgdCore:Avatar AS u ORDER BY u.dragonkills DESC, u.level DESC");

        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 25);

        return $this->render('@LotgdCore/list/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
