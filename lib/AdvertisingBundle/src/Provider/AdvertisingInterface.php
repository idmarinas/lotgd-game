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

interface AdvertisingInterface
{
    /**
     * Check if provider is active.
     */
    public function isEnabled(): bool;

    /**
     * Check if advertising module is enable.
     */
    public function isAdvertisingEnabled(): bool;

    /**
     * Enable advertising module.
     */
    public function enableAdvertising();

    /**
     * Disable advertising module.
     */
    public function disableAdvertising();

    /**
     * Add configuration for provider.
     */
    public function configure(array $config);

    /**
     * Get configuration of provider.
     */
    public function getConfig(): array;
}
