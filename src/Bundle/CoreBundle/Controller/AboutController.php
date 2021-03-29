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

use Lotgd\Bundle\Contract\LotgdBundleInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/about")
 */
class AboutController extends AbstractController
{
    public const TRANSLATOR_DOMAIN = 'lotgd_core_page_about';

    private $bundles;

    /**
     * @Route("/game/setup", name="lotgd_core_about_game_setup")
     */
    public function gameSetup(): Response
    {
        return $this->render('@LotgdCore/about/game_setup.html.twig');
    }

    /**
     * @Route("/bundles", name="lotgd_core_about_bundles")
     */
    public function bundles(): Response
    {
        return $this->render('@LotgdCore/about/bundles.html.twig', [
            'bundles_total_enabled' => \is_countable($this->bundles) ? \count($this->bundles) : 0,
            'bundles_lotgd_enabled' => \array_filter($this->bundles, function ($var)
            {
                return $var instanceof LotgdBundleInterface;
            }),
        ]);
    }

    public function setBundles(array $bundles): void
    {
        $this->bundles = $bundles;
    }
}
