<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.1.0
 */

namespace Lotgd\Core\Doctrine\ORM;

use Doctrine\Common\EventManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Laminas\Hydrator\ClassMethodsHydrator;

class EntityManager extends DoctrineEntityManager
{
    private $repositoryHydrator;

    /**
     * {@inheritdoc}
     */
    public static function create($connection, Configuration $config, ?EventManager $eventManager = null)
    {
        if ( $config->getMetadataDriverImpl() === null)
        {
            throw \Doctrine\ORM\ORMException::missingMappingDriverImpl();
        }

        $connection = static::createConnection($connection, $config, $eventManager);

        return new EntityManager($connection, $config, $connection->getEventManager());
    }

    /**
     * Check if has a connection with DataBase.
     */
    public function isConnected(): bool
    {
        return $this->getConnection()->isConnected();
    }

    /**
     * Hydrate an object by populating getter/setter methods.
     *
     * @return object
     */
    public function hydrateEntity(array $data, object $entity)
    {
        return $this->geLaminastHydrator()->hydrate($data, $entity);
    }

    /**
     * Extract values from an object with class methods.
     *
     * @param object|array $object
     */
    public function extractEntity($object): array
    {
        if (\is_array($object))
        {
            $set = [];

            foreach ($object as $key => $value)
            {
                $set[$key] = $this->extractEntity($value);
            }

            return $set;
        }
        elseif ( ! \is_object($object))
        {
            return (array) $object;
        }

        return $this->geLaminastHydrator()->extract($object);
    }

    /**
     * Get Hydrator instance.
     */
    private function geLaminastHydrator(): ClassMethodsHydrator
    {
        if ( ! $this->repositoryHydrator)
        {
            $this->repositoryHydrator = new ClassMethodsHydrator();
            //-- With this keyValue is keyValue. Otherwise it would be key_value
            $this->repositoryHydrator->removeNamingStrategy();
        }

        return $this->repositoryHydrator;
    }
}
