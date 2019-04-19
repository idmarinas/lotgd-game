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
use Lotgd\Core\Component\Filesystem;
use Tracy\Debugger;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Hydrator\ClassMethods;

/**
 * Script to upgrade a version.
 */
abstract class UpgradeAbstract
{
    use Pattern\Version;

    const DATA_DIR_UPDATE = __DIR__.'/data/update';

    const TRANSLATOR_DOMAIN = 'app-installer';
    const TRANSLATOR_KEY_TABLE_RENAME = 'upgrade.version.table.rename';
    const TRANSLATOR_KEY_TABLE_CREATE = 'upgrade.version.table.create';
    const TRANSLATOR_KEY_TABLE_IMPORT = 'upgrade.version.table.import';
    const TRANSLATOR_KEY_TABLE_DELETE = 'upgrade.version.table.delete';

    protected $doctrine;
    protected $connection;
    protected $messages = [];

    /**
     * Insert data of an update install.
     *
     * @param int $version Version to upgrade
     *
     * @return bool
     */
    public function insertData(int $version): bool
    {
        $filesystem = new Filesystem();
        $dir = self::DATA_DIR_UPDATE."/$version";
        $files = array_map(
            function ($value) use ($dir) { return "{$dir}/{$value}"; },
            $filesystem->listDir($dir)
        );

        if (0 == count($files))
        {
            $this->messages[] = \LotgdTranslator::t('upgrade.insertData.noFiles', ['version' => $this->getNamedVersion($version)], self::TRANSLATOR_DOMAIN);

            return true;
        }

        try
        {
            foreach ($files as $file)
            {
                $data = \json_decode(\file_get_contents($file), true);
                $entities = new HydratingResultSet(new ClassMethods(), new $data['entity']());
                $entities->initialize($data['rows']);

                foreach ($entities as $entity)
                {
                    $this->doctrine->merge($entity);
                }

                if ('insert' == $data['method'])
                {
                    $this->messages[] = \LotgdTranslator::t('insertData.data.insert', ['count' => count($data['rows']), 'table' => $data['table']], self::TRANSLATOR_DOMAIN);
                }
                elseif ('update' == $data['method'])
                {
                    $this->messages[] = \LotgdTranslator::t('insertData.data.update', ['count' => count($data['rows']), 'table' => $data['table']], self::TRANSLATOR_DOMAIN);
                }
                elseif ('replace' == $data['method'])
                {
                    $this->messages[] = \LotgdTranslator::t('insertData.data.update', ['count' => count($data['rows']), 'table' => $data['table']], self::TRANSLATOR_DOMAIN);
                }

                $this->doctrine->flush();
            }

            $this->doctrine->clear();

            return true;
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);
            $this->messages[] = \LotgdTranslator::t('upgrade.insertData.error', [], self::TRANSLATOR_DOMAIN);
            $this->messages[] = $th->getMessage();

            return false;
        }
    }

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
