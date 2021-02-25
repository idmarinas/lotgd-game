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

namespace Lotgd\Core\Controller;

use Lotgd\Bundle\Contract\LotgdBundleInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/about")
 */
class AboutController extends AbstractController
{
    public const TEXT_DOMAIN = 'lotgd_core_page_about';

    private $containerI;

    public function __construct(ContainerInterface $container)
    {
        $this->containerI = $container;
    }

    /**
     * @Route("/game/setup", name="lotgd_core_about_game_setup")
     */
    public function gameSetup(): Response
    {
        return $this->render('page/about/game_setup.html.twig');
    }

    /**
     * @Route("/bundles", name="lotgd_core_about_bundles")
     */
    public function bundles(): Response
    {
        $bundles = $this->containerI->get('kernel')->getBundles();

        return $this->render('page/about/bundles.html.twig', [
            'bundles_total_enabled' => \is_countable($bundles) ? \count($bundles) : 0,
            'bundles_lotgd_enabled' => \array_filter($bundles, function ($var)
            {
                return $var instanceof LotgdBundleInterface;
            }),
        ]);
    }
}
