<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Fixed;

use BadMethodCallException;
use LotgdResponse;
use Doctrine\ORM\Tools\SchemaTool;

class Doctrine
{
    use StaticTrait;

    /**
     * Add support for magic static method calls.
     *
     * @param string $name
     * @param array  $arguments
     * @param mixed  $method
     *
     * @return mixed the returned value from the resolved method
     */
    public static function __callStatic($method, $arguments)
    {
        if (method_exists(self::$instance, $method))
        {
            if ( ! self::$instance->isOpen())
            {
                self::$instance = self::$instance->create(self::$instance->getConnection(), self::$instance->getConfiguration());
            }

            return self::$instance->{$method}(...$arguments);
        }

        $methods = implode(', ', get_class_methods(self::$instance));

        throw new BadMethodCallException("Undefined method '{$method}'. The method name must be one of '{$methods}'");
    }

    /**
     * Alias of updateSchema.
     *
     * @param mixed|null $dumpSql
     */
    public static function syncEntity(string $entity, $dumpSql = null)
    {
        return self::updateSchema([$entity], $dumpSql);
    }

    /**
     * Alias of updateSchema.
     *
     * @param mixed|null $dumpSql
     */
    public static function syncEntities(array $entities, $dumpSql = null)
    {
        return self::updateSchema($entities, $dumpSql);
    }

    /**
     * Updates the database schema of the given classes by comparing the ClassMetadata
     * instances to the current database schema that is inspected.
     *
     * @param bool $dumpSql Dumps the generated SQL statements to the screen
     *
     * @return int Number of queries
     */
    public static function updateSchema(array $entities, $dumpSql = null)
    {
        $schemaTool = new SchemaTool(self::$instance);

        $metaData = [];

        foreach ($entities as $className)
        {
            $metaData[] = self::$instance->getMetadataFactory()->getMetadataFor($className);
        }
        $sqls = $schemaTool->getUpdateSchemaSql($metaData, true);

        if (empty($sqls))
        {
            LotgdResponse::pageDebug('Nothing to update - your database is already in sync with the current entities metadata.');

            return 0;
        }

        if ($dumpSql)
        {
            LotgdResponse::pageDebug(implode(';'.PHP_EOL, $sqls).';');
        }

        LotgdResponse::pageDebug('Updating database schema...');
        $schemaTool->updateSchema($metaData, true);

        $pluralization = (1 === \count($sqls)) ? 'query was' : 'queries were';

        LotgdResponse::pageDebug(sprintf('Database schema updated successfully! "<info>%s</info>" %s executed', \count($sqls), $pluralization));

        $proxyFactory = self::$instance->getProxyFactory();
        LotgdResponse::pageDebug(sprintf('Proxy classes generated to "%s"', $proxyFactory->generateProxyClasses($metaData)));

        return \count($sqls);
    }

    /**
     * Alias of updateSchema.
     *
     * @param mixed|null $dumpSql
     */
    public static function createSchema(array $entities, $dumpSql = null)
    {
        return self::updateSchema($entities, $dumpSql);
    }

    /**
     * Drops the database schema for the given classes.
     */
    public static function dropSchema(array $entities)
    {
        $schemaTool = new SchemaTool(self::$instance);

        $classes = [];

        foreach ($entities as $className)
        {
            $classes[] = self::$instance->getMetadataFactory()->getMetadataFor($className);
        }

        $schemaTool->dropSchema($classes);

        LotgdResponse::pageDebug(sprintf('Drop schemas for %s classes: "%s"', \count($entities), implode('", "', $entities)));
    }
}

class_alias('Lotgd\Core\Fixed\Doctrine', 'Doctrine', false);
