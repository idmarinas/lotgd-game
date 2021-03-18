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

namespace Lotgd\Bundle\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/game")
 *
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends AbstractController
{
    public const TRANSLATOR_DOMAIN = 'lotgd_user_page_profile';

    /**
     * @Route("/profile", name="lotgd_user_profile")
     */
    public function index(): Response
    {
        return $this->render('@LotgdUser/profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
}
