<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 3.0.0
 */

namespace Lotgd\Core;

use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\ZendConfigProvider;
use Zend\ServiceManager\Config as ServiceConfig;
use Zend\ServiceManager\ServiceManager as ZendServiceManager;

/**
 * Generated a Service Manager for Game.
 */
class ServiceManager extends ZendServiceManager
{
    /**
     * Production configuration for game.
     *
     * @var string
     */
    const LOTGD_CONFIG = 'config/lotgd.config.php';

    /**
     * Development configuration for game.
     *
     * @var string
     */
    const LOTGD_DEV_CONFIG = 'config/development.config.php';

    /**
     * Name of cache file.
     *
     * @var string
     */
    const CACHE_FILE = 'cache/service-manager.config.php';

    public function __construct()
    {
        $aggregator = new ConfigAggregator([
            new ZendConfigProvider(static::LOTGD_CONFIG),
            new ZendConfigProvider('config/autoload/global/{**/*,*}.php'),
            new ZendConfigProvider('config/autoload/local/{**/*,*}.php'),
            new ZendConfigProvider(static::LOTGD_DEV_CONFIG),
            new ZendConfigProvider('config/development/{,*}.php'),
        ], static::CACHE_FILE);

        $configuration = $aggregator->getMergedConfig();

        $config = $configuration['service_manager'] ?? [];
        $config = new ServiceConfig($config);

        $this->creationContext = $this;

        $config->configureServiceManager(parent::configure([]));

        $this->setService('GameConfig', $configuration);
    }
}
