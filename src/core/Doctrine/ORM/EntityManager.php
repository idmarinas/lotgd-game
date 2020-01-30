<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.1.0
 */

namespace Lotgd\Core\Doctrine\ORM;

use Doctrine\Common\EventManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;

class EntityManager extends DoctrineEntityManager
{
    private $isConnected;

    /**
     * {@inheritdoc}
     */
    public static function create($connection, Configuration $config, EventManager $eventManager = null)
    {
        if (! $config->getMetadataDriverImpl())
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
        if (
            defined('DB_NODB')
            || (null === $this->isConnected && ! file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT))
            || false === $this->isConnected
        ) {
            $this->isConnected = false;

            return false;
        }

        $this->isConnected = $this->getConnection()->ping();

        return $this->isConnected;
    }
}
