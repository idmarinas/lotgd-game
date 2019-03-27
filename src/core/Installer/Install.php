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

use Doctrine\ORM\Tools\SchemaTool;
use Lotgd\Core\Component\Filesystem;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Hydrator\ClassMethods;

/**
 * Script to install and update game.
 */
class Install
{
    use \Lotgd\Core\Pattern\Container;
    use Pattern\CanInstall;
    use Pattern\Modules;
    use Pattern\Progress;
    use Pattern\Upgrade;
    use Pattern\Version;

    const TRANSLATOR_DOMAIN = 'app-installer';
    const DATA_DIR_INSTALL = __DIR__.'/data/install';
    const DATA_DIR_UPDATE = __DIR__.'/data/update';

    /**
     * Make a upgrade install, avoid in clean install.
     *
     * @param int $version Is the actual version installed
     *
     * @return array
     */
    public function makeUpgradeInstall(int $version): array
    {
        $messages = [];
        $version = $this->getNextVersion($version); //-- Start with the next version

        //-- It is a clean installation not do nothing
        if (! $this->isUpgrade())
        {
            $messages[] = \LotgdTranslator::t('upgrade.nothing', [], self::TRANSLATOR_DOMAIN);

            return $messages;
        }

        try
        {
            do
            {
                $class = "Lotgd\Core\Installer\Upgrade\Version_{$version}\Upgrade";

                //-- Check all version to actual version
                if (! \class_exists($class))
                {
                    $version = $this->getNextVersion($version);

                    continue;
                }

                $upgrade = new $class();

                $upgrade->setDoctrine($this->getContainer(\Lotgd\Core\Db\Doctrine::class));

                $step = 1;
                $result = true;

                do
                {
                    $result = $upgrade->{"step{$step}"}();

                    $step++;
                } while ($result && \method_exists($upgrade, "step{$step}"));

                //-- Get all messages for this upgrade
                $messages = array_merge($messages, $upgrade->getMessages());

                $this->upgradedVersionOn($version);
                $version = $this->getNextVersion($version);
            } while ($version > 0 && ! $this->isUpgradedVersion($version));
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);
            $messages[] = \LotgdTranslator::t('upgrade.error', [], self::TRANSLATOR_DOMAIN);
            $messages[] = $th->getMessage();
        }

        return $messages;
    }

    /**
     * Synchronize the tables in the database.
     *
     * @return array
     */
    public function makeSynchronizationTables(): array
    {
        // return [];
        //-- Prepare for updating core tables
        $doctrine = $this->getContainer(\Lotgd\Core\Db\Doctrine::class);
        $classes = $doctrine->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($doctrine);
        $sqls = $schemaTool->getUpdateSchemaSql($classes, true);

        $messages = \LotgdTranslator::t('synchronizationTables.nothing', [], self::TRANSLATOR_DOMAIN);

        if (count($sqls))
        {
            $messages = [];
            $messages[] = '`@Updating database schema...`0`n';

            $schemaTool->updateSchema($classes, true);

            $messages[] = \LotgdTranslator::t('synchronizationTables.schema', ['count' => count($sqls)], self::TRANSLATOR_DOMAIN);
            $messages[] = \LotgdTranslator::t('synchronizationTables.proxy', ['classes' => $doctrine->getProxyFactory()->generateProxyClasses($classes)], self::TRANSLATOR_DOMAIN);
        }

        return $messages;
    }

    /**
     * Insert data of a clean install.
     *
     * @return array
     */
    public function makeInsertData(): array
    {
        $messages = [];
        //-- It is a clean or upgrade installation and the data has already been inserted
        if ($this->dataInserted() || $this->isUpgrade())
        {
            $messages[] = \LotgdTranslator::t('insertData.dataInserted', [], self::TRANSLATOR_DOMAIN);

            return $messages;
        }

        $filesystem = new Filesystem();
        $doctrine = $this->getContainer(\Lotgd\Core\Db\Doctrine::class);
        $files = array_map(
            function ($value) { return self::DATA_DIR_INSTALL.'/'.$value; },
            $filesystem->listDir(self::DATA_DIR_INSTALL)
        );

        if (0 == count($files))
        {
            $this->dataInsertedOff();
            $messages[] = \LotgdTranslator::t('insertData.noFiles', [], self::TRANSLATOR_DOMAIN);

            return $messages;
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
                    $doctrine->merge($entity);
                }

                if ('insert' == $data['method'])
                {
                    $messages[] = \LotgdTranslator::t('insertData.data.insert', ['count' => count($data['rows']), 'table' => $data['table']], self::TRANSLATOR_DOMAIN);
                }
                elseif ('update' == $data['method'])
                {
                    $messages[] = \LotgdTranslator::t('insertData.data.update', ['count' => count($data['rows']), 'table' => $data['table']], self::TRANSLATOR_DOMAIN);
                }
                elseif ('replace' == $data['method'])
                {
                    $messages[] = \LotgdTranslator::t('insertData.data.update', ['count' => count($data['rows']), 'table' => $data['table']], self::TRANSLATOR_DOMAIN);
                }

                $doctrine->flush();
            }

            $doctrine->clear();
            $this->dataInsertedOn();
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);
            $this->dataInsertedOff();
            $messages[] = \LotgdTranslator::t('insertData.error', [], self::TRANSLATOR_DOMAIN);
            $messages[] = $th->getMessage();
        }

        return $messages;
    }

    /**
     * Install the selected modules.
     *
     * @return array
     */
    public function makeInstallOfModules(): array
    {
        $messages = [];

        if ($this->skipModules())
        {
            $messages[] = \LotgdTranslator::t('installOfModules.skipped', [], self::TRANSLATOR_DOMAIN);

            return $messages;
        }

        $modules = $this->getModules();
        reset($modules);

        if (! count($modules))
        {
            $messages[] = '`QNot modules found to process.`0`n';

            return $messages;
        }

        foreach ($modules as $modulename => $options)
        {
            $ops = explode(',', $options);
            reset($ops);

            foreach ($ops as $op)
            {
                $this->moduleProcess($op, $modulename, $messages);
            }
        }

        return $messages;
    }

    /**
     * Process with the module.
     *
     * @param string $type
     * @param string $modulename
     * @param array  $messages
     */
    protected function moduleProcess($type, $modulename, &$messages)
    {
        switch ($type)
        {
            case 'uninstall':
                $result = uninstall_module($modulename);
                $messages[] = \LotgdTranslator::t('installOfModules.process.uninstall', ['module' => $modulename, 'result' => $result], self::TRANSLATOR_DOMAIN);
            break;
            case 'install':
                $result = install_module($modulename);
                $messages[] = \LotgdTranslator::t('installOfModules.process.install', ['module' => $modulename, 'result' => $result], self::TRANSLATOR_DOMAIN);
            break;
            case 'activate':
                $result = activate_module($modulename);
                $messages[] = \LotgdTranslator::t('installOfModules.process.activate', ['module' => $modulename, 'result' => $result], self::TRANSLATOR_DOMAIN);
            break;
            case 'deactivate':
                $result = deactivate_module($modulename);
                $messages[] = \LotgdTranslator::t('installOfModules.process.deactivate', ['module' => $modulename, 'result' => $result], self::TRANSLATOR_DOMAIN);
            break;
            case 'donothing':
                $messages[] = \LotgdTranslator::t('installOfModules.process.donothing', ['module' => $modulename], self::TRANSLATOR_DOMAIN);
            break;
            default: break;
        }
    }
}
