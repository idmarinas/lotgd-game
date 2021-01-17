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

use Lotgd\Core\Pattern as PatternCore;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class AdvertisingGoogle extends AbstractExtension implements GlobalsInterface
{
    use PatternCore\Template;

    protected $adsGoogle;
    protected $templatePartialsBlock;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('google_ad', [$this, 'showGoogleAd']),
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
    public function showGoogleAd(string $adSlot): string
    {
        $ad = $this->getAdsenseService()->getSlot($adSlot);

        if ($ad)
        {
            return $this->getTemplateBlock()->renderBlock('ad_wrapper', ['ad_content' => $ad]);
        }

        return '';
    }

    /**
     * Determine if Google AdSense is active.
     */
    public function advertisingGoogleIsActived(): bool
    {
        return $this->getAdsenseService()->isEnabled();
    }

    protected function getAdsenseService()
    {
        if ( ! $this->adsGoogle)
        {
            $this->adsGoogle = $this->getService('lotgd_core_advertising.adsense');
        }

        return $this->adsGoogle;
    }

    /**
     * Template block for partials.
     * Only load one time.
     */
    protected function getTemplateBlock()
    {
        if ( ! $this->templatePartialsBlock)
        {
            $this->templatePartialsBlock = $this->getTemplate()->load('{theme}/_blocks/_partials.html.twig');
        }

        return $this->templatePartialsBlock;
    }
}
