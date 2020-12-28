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
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Hydrator\ClassMethodsHydrator;
use Symfony\Component\Finder\Finder;
use Tracy\Debugger;

/**
 * Script to upgrade a version.
 */
abstract class UpgradeAbstract
{
    use Pattern\Version;

    public const VERSION_NUMBER = -1;

    public const DATA_DIR_UPDATE = __DIR__.'/data/update';

    public const TRANSLATOR_DOMAIN           = 'app-installer';
    public const TRANSLATOR_KEY_TABLE_RENAME = 'upgrade.version.table.rename';
    public const TRANSLATOR_KEY_TABLE_CREATE = 'upgrade.version.table.create';
    public const TRANSLATOR_KEY_TABLE_IMPORT = 'upgrade.version.table.import';
    public const TRANSLATOR_KEY_TABLE_DELETE = 'upgrade.version.table.delete';

    protected $doctrine;
    protected $connection;
    protected $messages = [];

    /**
     * First step of upgraded.
     */
    final public function step0(): bool
    {
        $this->messages[] = \LotgdTranslator::t('upgrade.version.to', ['version' => $this->getNameVersion(static::VERSION_NUMBER)], self::TRANSLATOR_DOMAIN);

        return true;
    }

    /**
     * Insert data of an update install.
     *
     * @param int $version Version to upgrade
     */
    public function insertData(int $version): bool
    {
        $dir    = self::DATA_DIR_UPDATE."/{$version}";
        $finder = new Finder();
        $files  = $finder->files()->in($dir);

        if (0 == \count($files))
        {
            $this->messages[] = \LotgdTranslator::t('upgrade.insertData.noFiles', ['version' => $this->getNameVersion($version)], self::TRANSLATOR_DOMAIN);

            return true;
        }

        try
        {
            foreach ($files as $file)
            {
                $file = (string) $file;

                $data     = \json_decode(\file_get_contents($file), true);
                $entities = new HydratingResultSet(new ClassMethodsHydrator(), new $data['entity']());
                $entities->initialize($data['rows']);

                foreach ($entities as $entity)
                {
                    $this->doctrine->merge($entity);
                }

                if ('insert' == $data['method'])
                {
                    $this->messages[] = \LotgdTranslator::t('insertData.data.insert', ['count' => \count($data['rows']), 'table' => $data['table']], self::TRANSLATOR_DOMAIN);
                }
                elseif ('update' == $data['method'] || 'replace' == $data['method'])
                {
                    $this->messages[] = \LotgdTranslator::t('insertData.data.update', ['count' => \count($data['rows']), 'table' => $data['table']], self::TRANSLATOR_DOMAIN);
                }

                $this->doctrine->flush();
            }

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
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Set Doctrine Entity Manager.
     */
    public function setDoctrine(EntityManager $doctrine): self
    {
        $this->doctrine = $doctrine;

        $this->setConnection($doctrine->getConnection());

        return $this;
    }

    /**
     * Set connection.
     */
    public function setConnection(Connection $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Sync a single entity.
     */
    public function syncEntity(string $entity): bool
    {
        try
        {
            $schemaTool = new SchemaTool($this->doctrine);
            $metaData   = $this->doctrine->getMetadataFactory()->getMetadataFor($entity);
            $sqls       = $schemaTool->getUpdateSchemaSql([$metaData], true);

            if (0 === \count($sqls))
            {
                $this->messages[] = \LotgdTranslator::t('upgrade.version.nothing', [], self::TRANSLATOR_DOMAIN);

                return true;
            }

            $schemaTool->updateSchema([$metaData], true);

            $this->messages[] = \LotgdTranslator::t('upgrade.version.schema', ['count' => \count($sqls)], self::TRANSLATOR_DOMAIN);

            $proxyFactory     = $this->doctrine->getProxyFactory();
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
