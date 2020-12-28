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
    public function showGoogleAd(string $adId): string
    {
        $config   = $this->getAdsConfig();
        $adConfig = $config[$adId] ?? null;

        //-- Google AdSense is disabled
        //-- Not found Ad config
        //-- Slot not isset or are empty
        if ( ! $this->advertisingGoogleIsActived() || ! $adConfig || ! isset($adConfig['slot']) || ! $adConfig['slot'])
        {
            return '';
        }

        $ins = \sprintf(
            '<ins class="adsbygoogle"
            style="%1$s"
            data-ad-client="%2$s"
            data-ad-slot="%3$s"
            data-ad-format="%4$s"
            data-full-width-responsive="%5$s"></ins>',
            $adConfig['style'] ?? '',
            $config['client'],
            $adConfig['slot'],
            $adConfig['format'],
            $adConfig['responsive']
        );
        $script = '<script> (adsbygoogle = window.adsbygoogle || []).push({}); </script>';

        return $this->getTemplateBlock()->renderBlock('ad_wrapper', ['ad_content' => $ins.$script]);
    }

    /**
     * Determine if Google AdSense is active.
     */
    public function advertisingGoogleIsActived(): bool
    {
        $config = $this->getAdsConfig();

        return isset($config['client']) && $config['client'];
    }

    protected function getAdsConfig()
    {
        if ( ! $this->adsGoogle)
        {
            $config          = $this->getContainer('GameConfig');
            $this->adsGoogle = $config['advertising']['google'] ?? [];
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
