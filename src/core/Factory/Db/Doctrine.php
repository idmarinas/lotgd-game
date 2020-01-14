<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Factory\Db;

use Doctrine\Common\{
    Cache as DoctrineCache,
    EventManager as DoctrineEventManager,
    Proxy\AbstractProxyFactory
};
use Doctrine\ORM\{
    Configuration as DoctrineConfiguration,
    EntityManager as DoctrineEntityManager,
    Events as DoctrineEvents,
    Mapping\UnderscoreNamingStrategy as DoctrineUnderscoreNamingStrategy
};
use Interop\Container\ContainerInterface;
use Lotgd\Core\Doctrine\{
    Extension\TablePrefix as DoctrineTablePrefix,
    Strategy\Quote as DoctrineQuoteStrategy
};
use Zend\ServiceManager\{
    Factory\FactoryInterface,
    ServiceLocatorInterface
};

class Doctrine implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get('GameConfig')['lotgd_core'];
        $adapter = $options['db']['adapter'] ?? [];
        $adapter = is_array($adapter) ? $adapter : [];
        $isDevelopment = (bool) ($options['development'] ?? false);
        $doctrine = $options['doctrine'] ?? [];
        $cacheDir = "storage/cache/doctrine";

        $doctrineCache = new DoctrineCache\ArrayCache();

        if (! $isDevelopment)
        {
            $doctrineCache = new DoctrineCache\FilesystemCache("{$cacheDir}/Cache");

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
            'charset' => $adapter['charset'] ?? 'utf8mb4',
            'collate' => $adapter['collate'] ?? 'utf8mb4_general_ci'
        ];

        $config = new DoctrineConfiguration();
        $config->setMetadataCacheImpl($doctrineCache);
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(['src/core/Entity', 'src/local/Entity'], false));
        $config->setQueryCacheImpl($doctrineCache);
        $config->setProxyDir("{$cacheDir}/Proxy");
        $config->setProxyNamespace('Lotgd\Proxies');
        $config->setAutoGenerateProxyClasses(($isDevelopment ?: AbstractProxyFactory::AUTOGENERATE_FILE_NOT_EXISTS));

        //-- Alias for entities Core and Local
        $config->addEntityNamespace('LotgdCore', 'Lotgd\Core\Entity');
        $config->addEntityNamespace('LotgdLocal', 'Lotgd\Local\Entity');

        //-- Strategy
        $config->setNamingStrategy(new DoctrineUnderscoreNamingStrategy(CASE_LOWER));
        $config->setQuoteStrategy(new DoctrineQuoteStrategy());

        //-- DQL Functions
        $config->setCustomDatetimeFunctions([
            'month' => \DoctrineExtensions\Query\Mysql\Month::class,
            'year' => \DoctrineExtensions\Query\Mysql\Year::class,
            'date' => \DoctrineExtensions\Query\Mysql\Date::class
        ]);
        $config->setCustomNumericFunctions([
            'round' => \DoctrineExtensions\Query\Mysql\Round::class,
            'rand' => \DoctrineExtensions\Query\Mysql\Rand::class,
        ]);
        $config->setCustomStringFunctions([
            'inet_aton' => \DoctrineExtensions\Query\Mysql\InetAton::class
        ]);

        //-- Default EntityRepository for all Entities
        $config->setDefaultRepositoryClassName(\Lotgd\Core\Doctrine\ORM\EntityRepository::class);

        $evm = new DoctrineEventManager();
        $tablePrefix = new DoctrineTablePrefix(($options['db']['prefix'] ?? ''));
        $evm->addEventListener(DoctrineEvents::loadClassMetadata, $tablePrefix);

        //-- Entity Manager of Doctrine
        $entityManager = DoctrineEntityManager::create($dbParams, $config, $evm);

        //-- Add Sql requests made by Doctrine in the Tracy debugger bar.
        if ($isDevelopment)
        {
            \MacFJA\Tracy\DoctrineSql::init($entityManager);
        }

        return $entityManager;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
