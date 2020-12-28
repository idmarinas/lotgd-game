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

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\LaminasConfigProvider;
use Laminas\ServiceManager\Config as ServiceConfig;
use Laminas\ServiceManager\ServiceManager as ZendServiceManager;

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
    public const LOTGD_CONFIG = 'config/lotgd.config.php';

    /**
     * Development configuration for game.
     *
     * @var string
     */
    public const LOTGD_DEV_CONFIG = 'config/development.config.php';

    /**
     * Name of cache file.
     *
     * @var string
     */
    public const CACHE_FILE = 'storage/cache/service-manager.config.php';

    public function __construct()
    {
        $aggregator = new ConfigAggregator([
            new LaminasConfigProvider(static::LOTGD_CONFIG),
            new LaminasConfigProvider('config/autoload/global/{**/*,*}.php'),
            new LaminasConfigProvider('config/autoload/local/{**/*,*}.php'),
            new LaminasConfigProvider(static::LOTGD_DEV_CONFIG),
            new LaminasConfigProvider('config/development/{,*}.php'),
        ], static::CACHE_FILE);

        $configuration = $aggregator->getMergedConfig();

        $config = $configuration['service_manager'] ?? [];
        $config = new ServiceConfig($config);

        $this->creationContext = $this;

        $config->configureServiceManager(parent::configure([]));

        $this->setService('GameConfig', $configuration);
        $this->setService('gameconfig', $configuration);
        $this->setService('Config', $configuration);
        $this->setService('config', $configuration);
    }
}
