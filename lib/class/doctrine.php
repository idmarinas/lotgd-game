<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

class Doctrine
{
    protected static $wrapper;

    /**
     * Get a repository of Doctrine.
     *
     * @param string $class
     *
     * @return object
     */
    public static function getRepository(string $class)
    {
        return self::$wrapper->getRepository($class);
    }

    /**
     * Creates a new Query object.
     *
     * @param string $dql the DQL string
     *
     * @return Query
     */
    public static function createQuery($dql = '')
    {
        return self::$wrapper->createQuery($dql);
    }

    /**
     * Creates a native SQL query.
     *
     * @param string           $sql
     * @param ResultSetMapping $rsm the ResultSetMapping to use
     *
     * @return NativeQuery
     */
    public static function createNativeQuery($dql = '')
    {
        return self::$wrapper->createNativeQuery($dql);
    }

    /**
     * Tells the EntityManager to make an instance managed and persistent.
     *
     * The entity will be entered into the database at or before transaction
     * commit or as a result of the flush operation.
     *
     * @param object $entity the instance to make managed and persistent
     */
    public static function persist($entity)
    {
        self::$wrapper->persist($entity);
    }

    /**
     * Removes an entity instance.
     *
     * A removed entity will be removed from the database at or before transaction commit
     * or as a result of the flush operation.
     *
     * @param object $entity the entity instance to remove
     */
    public static function remove($entity)
    {
        self::$wrapper->remove($entity);
    }

    /**
     * Refreshes the persistent state of an entity from the database,
     * overriding any local changes that have not yet been persisted.
     *
     * @param object $entity the entity to refresh
     */
    public static function refresh($entity)
    {
        self::$wrapper->refresh($entity);
    }

    /**
     * Flushes all changes to objects that have been queued up to now to the database.
     * This effectively synchronizes the in-memory state of managed objects with the
     * database.
     *
     * If an entity is explicitly passed to this method only this entity and
     * the cascade-persist semantics + scheduled inserts/removals are synchronized.
     *
     * @param null|object|array $entity
     */
    public static function flush($entity = null)
    {
        self::$wrapper->flush($entity);
    }

    /**
     * Clears the EntityManager. All entities that are currently managed
     * by this EntityManager become detached.
     *
     * @param string|null $entityName if given, only entities of this type will get detached
     */
    public static function clear($entityName = null)
    {
        self::$wrapper->clear($entityName);
    }

    /**
     * Synchronize a Entity with database.
     *
     * @param string $entity
     * @param bool   $dumpSql Dumps the generated SQL statements to the screen
     *
     * @return int Number of queries
     */
    public static function syncEntity(string $entity, $dumpSql = false)
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
    public static function syncEntities(array $entities, $dumpSql = false)
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
     * Add wrapper to script.
     *
     * @param Doctrine\ORM\EntityManager $wrapper
     */
    public static function wrapper(EntityManager $wrapper)
    {
        self::$wrapper = $wrapper;
    }
}

//-- Configure DB
Doctrine::wrapper(LotgdLocator::get(Lotgd\Core\Lib\Doctrine::class));
