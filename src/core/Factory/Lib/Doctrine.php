<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Factory\Lib;

use Doctrine\Common\Cache as DoctrineCache;
use Doctrine\Common\EventManager as DoctrineEventManager;
use Doctrine\Common\Proxy\AbstractProxyFactory;
use Doctrine\ORM\Configuration as DoctrineConfiguration;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\Events as DoctrineEvents;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy as DoctrineUnderscoreNamingStrategy;
use Interop\Container\ContainerInterface;
use Lotgd\Core\Doctrine\Extension\TablePrefix as DoctrineTablePrefix;
use Lotgd\Core\Doctrine\Strategy\Quote as DoctrineQuoteStrategy;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Doctrine implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get('GameConfig')['lotgd_core'];
        $adapter = $options['db']['adapter'] ?? [];
        $adapter = is_array($adapter) ? $adapter : [];
        $isDevelopment = (bool) ($options['development'] ?? false);
        $doctrine = $options['doctrine'] ?? [];

        $doctrineCache = new DoctrineCache\ArrayCache();
        if (! $isDevelopment)
        {
            $cacheDir = $options['cache']['config']['cache_dir'] ?? 'cache';
            $doctrineCache = new DoctrineCache\FilesystemCache(trim($cacheDir, '/').'/doctrine');

            if (isset($doctrine['cache_class']) && class_exists($doctrine['cache_class']))
            {
                $doctrineCache = new $doctrine['cache_class']();
            }
        }

        $dbParams = [
            'driver' => strtolower($adapter['driver'] ?? 'Pdo_Mysql'), //-- By default is always mysql
            'user' => $adapter['username'] ?? '',
            'password' => $adapter['password'] ?? '',
            'dbname' => $adapter['database'] ?? '',
            'charset' => $adapter['charset'] ?? ''
        ];

        $config = new DoctrineConfiguration();
        $config->setMetadataCacheImpl($doctrineCache);
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(['src/core/Entity', 'src/local/Entity'], false));
        $config->setQueryCacheImpl($doctrineCache);
        $config->setProxyDir($doctrine['proxy_dir'] ?? 'doctrine/Proxy');
        $config->setProxyNamespace('Lotgd\Proxies');
        $config->setAutoGenerateProxyClasses(($isDevelopment ? true : AbstractProxyFactory::AUTOGENERATE_FILE_NOT_EXISTS));

        $config->setNamingStrategy(new DoctrineUnderscoreNamingStrategy(CASE_LOWER));
        $config->setQuoteStrategy(new DoctrineQuoteStrategy());

        $evm = new DoctrineEventManager();
        $tablePrefix = new DoctrineTablePrefix(($options['db']['prefix'] ?? ''));
        $evm->addEventListener(DoctrineEvents::loadClassMetadata, $tablePrefix);

        return DoctrineEntityManager::create($dbParams, $config, $evm);
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
