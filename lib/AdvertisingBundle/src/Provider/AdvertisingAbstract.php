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

abstract class AdvertisingAbstract implements AdvertisingInterface
{
    /**
     * Configuration for Provider.
     *
     * @var array
     */
    protected $configuration;

    /**
     * Indicate if advertising bundle is enable or disabled.
     *
     * @var bool
     */
    protected $advertisingEnable;

    /**
     * {@inheritDoc}
     */
    public function isEnabled(): bool
    {
        return $this->isAdvertisingEnabled() && $this->configuration['enable'];
    }

    /**
     * {@inheritDoc}
     */
    public function isAdvertisingEnabled(): bool
    {
        return $this->advertisingEnable;
    }

    /**
     * {@inheritDoc}
     */
    public function enableAdvertising(): self
    {
        $this->advertisingEnable = true;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function disableAdvertising(): self
    {
        $this->advertisingEnable = false;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function configure(array $config): self
    {
        $this->configuration = $config;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig(): array
    {
        return $this->configuration;
    }
}
