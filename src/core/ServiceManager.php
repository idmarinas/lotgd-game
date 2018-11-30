<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core;

use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ValueGenerator;
use Zend\Config\Config;
use Zend\Config\Factory as ConfigFactory;
use Zend\ServiceManager\Config as ServiceConfig;
use Zend\ServiceManager\ServiceManager as ZendServiceManager;
use Zend\Stdlib\Glob;

class ServiceManager extends ZendServiceManager
{
    public function __construct(array $configuration)
    {
        $configuration = $this->processConfig(new Config($configuration, true));

        $config = $configuration['service_manager'] ?? [];
        $config = new ServiceConfig($config);

        $this->creationContext = $this;

        $config->configureServiceManager(parent::configure([]));

        $this->setService('GameConfig', $configuration);
    }

    /**
     * Process configuration data.
     *
     * @param \Zend\Config\Config $configuration
     *
     * @return array
     */
    private function processConfig(Config $configuration): array
    {
        $configuration = $this->processGlobPathsConfig($configuration);

        //-- Cache configuration for performance
        if ($configuration->lotgd_core->cache_config)
        {
            if (! file_exists('cache/service-manager.config.php'))
            {
                $this->genererateFileCache($configuration->toArray());
            }

            return require 'cache/service-manager.config.php';
        }

        return $configuration->toArray();
    }

    /**
     * Process all Glob paths in config.
     *
     * @param \Zend\Config\Config $configuration
     *
     * @return \Zend\Config\Config
     */
    private function processGlobPathsConfig(Config $configuration): Config
    {
        if ($configuration->config_glob_paths)
        {
            foreach ($configuration->config_glob_paths as $path)
            {
                foreach (Glob::glob($path, Glob::GLOB_BRACE) as $file)
                {
                    $configuration->merge(ConfigFactory::fromFile($file, true));
                }
            }
        }

        return $configuration;
    }

    /**
     * Generate file for cache configuration.
     *
     * @param array $configuration
     */
    private function genererateFileCache(array $configuration)
    {
        $file = FileGenerator::fromArray([
            'docblock' => DocBlockGenerator::fromArray([
                'shortDescription' => 'This file is automatically created',
                'longDescription' => null,
                'tags' => [
                    [
                        'name' => 'create',
                        'description' => date('M d, Y h:i a'),
                    ],
                ]
            ]),
            'body' => 'return '.new ValueGenerator($configuration, ValueGenerator::TYPE_ARRAY_SHORT).';'
        ]);

        return file_put_contents('cache/service-manager.config.php', $file->generate());
    }
}
