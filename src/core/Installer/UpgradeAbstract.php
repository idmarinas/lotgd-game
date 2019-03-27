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

namespace Lotgd\Core\Installer;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Tracy\Debugger;

/**
 * Script to upgrade a version.
 */
abstract class UpgradeAbstract
{
    const TRANSLATOR_DOMAIN = 'app-installer';
    protected $doctrine;
    protected $connection;
    protected $messages = [];

    /**
     * Get all generated messages.
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Set Doctrine Entity Manager.
     *
     * @param EntityManager $doctrine
     *
     * @return self
     */
    public function setDoctrine(EntityManager $doctrine): self
    {
        $this->doctrine = $doctrine;

        $this->setConnection($doctrine->getConnection());

        return $this;
    }

    /**
     * Set connection.
     *
     * @param Connection $connection
     *
     * @return self
     */
    public function setConnection(Connection $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Sync a single entity.
     *
     * @param string $entity
     *
     * @return bool
     */
    public function syncEntity(string $entity): bool
    {
        try
        {
            $schemaTool = new SchemaTool($this->doctrine);
            $metaData = $this->doctrine->getMetadataFactory()->getMetadataFor($entity);
            $sqls = $schemaTool->getUpdateSchemaSql([$metaData], true);

            if (0 === count($sqls))
            {
                $this->messages[] = \LotgdTranslator::t('upgrade.version.nothing', [], self::TRANSLATOR_DOMAIN);

                return true;
            }

            $schemaTool->updateSchema([$metaData], true);

            $this->messages[] = \LotgdTranslator::t('upgrade.version.schema', ['count' => count($sqls)], self::TRANSLATOR_DOMAIN);

            $proxyFactory = $this->doctrine->getProxyFactory();
            $this->messages[] = \LotgdTranslator::t('upgrade.version.proxy', ['classes' => $proxyFactory->generateProxyClasses([$metaData])], self::TRANSLATOR_DOMAIN);

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }
}
