<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.6.0
 */

namespace Lotgd\Core\Twig\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;
use LotgdCore\AdvertisingBundle\Provider\AdsenseAdvertising;

class AdvertisingGoogle extends AbstractExtension implements GlobalsInterface
{
    protected $adsGoogle;
    protected $templatePartialsBlock;

    public function __construct(AdsenseAdvertising $adsGoogle)
    {
        $this->adsGoogle = $adsGoogle;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('google_ad', [$this, 'showGoogleAd'], ['needs_environment' => true]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return [
            'google_ads_is_active' => $this->advertisingGoogleIsActived(),
        ];
    }

    /**
     * Show ad of Google.
     */
    public function showGoogleAd(Environment $env, string $adSlot): string
    {
        $ad = $this->adsGoogle->getSlot($adSlot);

        if ($ad)
        {
            return $env->load('_blocks/_partials.html.twig')->renderBlock('ad_wrapper', ['ad_content' => $ad]);
        }

        return '';
    }

    /**
     * Determine if Google AdSense is active.
     */
    public function advertisingGoogleIsActived(): bool
    {
        return $this->adsGoogle->isEnabled();
    }
}
