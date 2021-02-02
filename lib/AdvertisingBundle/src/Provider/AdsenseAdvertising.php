<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.11.0
 */

namespace LotgdCore\AdvertisingBundle\Provider;

use Laminas\View\Helper\InlineScript;

final class AdsenseAdvertising extends AdvertisingAbstract
{
    /**
     * Indicate if has an ads.
     *
     * @var bool
     */
    protected $hasAds = false;

    /**
     * Indicate if script is rendered.
     *
     * @var bool
     */
    protected $scriptRendered = false;

    /**
     * @var InlineScript
     */
    protected $inlineScript;

    /**
     * Environment of app.
     *
     * @var string
     */
    protected $environment;

    public function __construct(InlineScript $inlineScript, string $environment)
    {
        $this->inlineScript = $inlineScript;
        $this->environment  = $environment;
    }

    public function getSlot(string $slot)
    {
        //-- If not enable return empty string.
        if ( ! $this->isEnabled())
        {
            return '';
        }

        $config     = $this->getConfig();
        $slotConfig = $config['banners'][$slot] ?? [];

        if (empty($slotConfig) || ! $slotConfig['slot'])
        {
            return '';
        }

        $this->hasAds = true;

        $this->addGoogleAdsenseScript();

        return \sprintf(
            '<ins class="adsbygoogle"
                style="%1$s"
                data-ad-client="%2$s"
                data-ad-slot="%3$s"
                data-ad-format="%4$s"
                data-full-width-responsive="%5$s"></ins>
            <script> (adsbygoogle = window.adsbygoogle || []).push({}); </script>
            ',
            $slotConfig['style'] ?? '',
            $config['client'],
            $slotConfig['slot'],
            $slotConfig['format'],
            $slotConfig['responsive'] ? 'true' : 'false'
        );
    }

    /**
     * Is enable if enable is true and client are configured.
     */
    public function isEnabled(): bool
    {
        return parent::isEnabled() && \is_string($this->configuration['client']) && ! empty($this->configuration['client']);
    }

    private function addGoogleAdsenseScript()
    {
        if ($this->hasAds && ! $this->scriptRendered && 'prod' == $this->environment)
        {
            $this->scriptRendered = true;
            $this->inlineScript->appendFile('https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js');
        }
    }
}
