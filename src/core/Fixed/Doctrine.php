<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Fixed;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

class Doctrine
{
    protected static $wrapper;

    /**
     * Add support for magic static method calls.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed the returned value from the resolved method
     */
    public static function __callStatic($method, $arguments)
    {
        if (\method_exists(self::$wrapper, $method))
        {
            return self::$wrapper->{$method}(...$arguments);
        }

        $methods = implode(', ', get_class_methods(self::$wrapper));

        throw new \BadMethodCallException("Undefined method '$method'. The method name must be one of '$methods'");
    }

    /**
     * Synchronize a Entity with database.
     *
     * @param string $entity
     * @param bool   $dumpSql Dumps the generated SQL statements to the screen
     *
     * @return int Number of queries
     */
    public static function syncEntity(string $entity, $dumpSql = null)
    {
        $schemaTool = new SchemaTool(self::$wrapper);
        $metaData = self::$wrapper->getMetadataFactory()->getMetadataFor($entity);
        $sqls = $schemaTool->getUpdateSchemaSql([$metaData], true);

        if (0 === count($sqls))
        {
            debug('Nothing to update - your database is already in sync with the current entity metadata.');

            return 0;
        }

        if ($dumpSql)
        {
            debug(implode(';'.PHP_EOL, $sqls).';');
        }

        debug('Updating database schema...');
        $schemaTool->updateSchema([$metaData], true);

        $pluralization = (1 === count($sqls)) ? 'query was' : 'queries were';

        debug(sprintf('Database schema updated successfully! "%s" %s executed', count($sqls), $pluralization));

        $proxyFactory = self::$wrapper->getProxyFactory();
        debug(sprintf('Proxy classes generated to "%s"', $proxyFactory->generateProxyClasses([$metaData])));

        return count($sqls);
    }

    /**
     * Synchronizes an array of Entities with database.
     *
     * @param array $entities
     * @param bool  $dumpSql  Dumps the generated SQL statements to the screen
     *
     * @return int Number of queries
     */
    public static function syncEntities(array $entities, $dumpSql = null)
    {
        $schemaTool = new SchemaTool(self::$wrapper);

        $metaData = [];

        foreach ($entities as $key => $className)
        {
            $metaData[] = self::$wrapper->getMetadataFactory()->getMetadataFor($className);
        }
        $sqls = $schemaTool->getUpdateSchemaSql($metaData, true);

        if (0 === count($sqls))
        {
            debug('Nothing to update - your database is already in sync with the current entities metadata.');

            return 0;
        }

        if ($dumpSql)
        {
            debug(implode(';'.PHP_EOL, $sqls).';');
        }

        debug('Updating database schema...');
        $schemaTool->updateSchema($metaData, true);

        $pluralization = (1 === count($sqls)) ? 'query was' : 'queries were';

        debug(sprintf('Database schema updated successfully! "<info>%s</info>" %s executed', count($sqls), $pluralization));

        $proxyFactory = self::$wrapper->getProxyFactory();
        debug(sprintf('Proxy classes generated to "%s"', $proxyFactory->generateProxyClasses($metaData)));

        return count($sqls);
    }

    /**
     * Drops the database schema for the given classes.
     */
    public function dropSchema(array $entities)
    {
        $schemaTool = new SchemaTool(self::$wrapper);

        $classes = [];

        foreach ($entities as $className)
        {
            $classes[] = self::$wrapper->getMetadataFactory()->getMetadataFor($className);
        }

        $schemaTool->dropSchema($classes);

        debug(sprintf('Drop schemas for %s classes: "%s"', count($entities), implode('", "', $entities)));
    }

    /**
     * Add wrapper to script.
     *
     * @param Doctrine\ORM\EntityManager $wrapper
     */
    public static function wrapper(EntityManager $wrapper)
    {
        self::$wrapper = $wrapper;
    }
}

class_alias('Lotgd\Core\Fixed\Doctrine', 'Doctrine', false);
