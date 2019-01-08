<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Installer;

use Doctrine\ORM\Tools\SchemaTool;
use Lotgd\Core\Component\Filesystem;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Hydrator\ClassMethods;

class Install
{
    use \Lotgd\Core\Pattern\Container;
    use Pattern\Version;
    use Pattern\IsUpgrade;
    use Pattern\Modules;
    use Pattern\Progress;
    use Pattern\CanInstall;

    const DATA_DIR = __DIR__.'/data/install';

    /**
     * Make a pre-instal, This prepare database for new structure of tables.
     *
     * Not used for now.
     */
    public function makePreInstall()
    {
        return ['`QNothing to do.`0`n'];
    }

    /**
     * Synchronize the tables in the database.
     *
     * @return array
     */
    public function makeSynchronizationTables(): array
    {
        //-- Prepare for updating core tables
        $doctrine = $this->getContainer(\Lotgd\Core\Db\Doctrine::class);
        $classes = $doctrine->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($doctrine);
        $sqls = $schemaTool->getUpdateSchemaSql($classes, true);

        $messages = ['`@Nothing to update - your database is already in sync with the current entities metadata.`0`n'];

        if (count($sqls))
        {
            $messages = [];
            $messages[] = '`@Updating database schema...`0`n';

            $schemaTool->updateSchema($classes, true);

            $pluralization = (1 === count($sqls)) ? 'query was' : 'queries were';

            $messages[] = ['`@Database schema updated successfully! "`b`2%s`0`b" %s executed.`0`n', count($sqls), $pluralization];
            $messages[] = ['`@Proxy classes generated "`b`2%s`0´b".`0`n', $doctrine->getProxyFactory()->generateProxyClasses($classes)];
        }

        return $messages;
    }

    /**
     * Insert data in tables.
     *
     * @return array
     */
    public function makeInsertData(): array
    {
        $messages = [];

        if ($this->dataInserted())
        {
            $messages[] = '`QIt is not necessary to re-insert the data`0`n';

            return $messages;
        }

        //-- Is a upgrade
        if ($this->isUpgrade())
        {
            return $this->makeUpgradeInsertData();
        }

        $filesystem = new Filesystem();
        $doctrine = $this->getContainer(\Lotgd\Core\Db\Doctrine::class);
        $files = array_map(function ($value) { return self::DATA_DIR.'/'.$value; }, $filesystem->listDir(self::DATA_DIR));

        if (0 == count($files))
        {
            $this->dataInsertedOff();
            $messages[] = "`2No data files were found, game data cannot be inserted. Please check this out and report.`0`n";

            return $messages;
        }

        try
        {
            foreach ($files as $key => $file)
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
                    $messages[] = ['`@`iInserted´i a total of `2`b%s´b`0 row/s in table "`2`b%s´b`0".`0`n', count($data['rows']), $data['table']];
                }
                elseif ('update' == $data['method'])
                {
                    $messages[] = ['`@`iUpdated´i a total of `2`b%s´b`0 row/s in table "`2`b%s´b`0".`0`n', count($data['rows']), $data['table']];
                }
                elseif ('replace' == $data['method'])
                {
                    $messages[] = ['`@`iReplaced´i a total of `2`b%s´b`0 row/s in table "`2`b%s´b`0".`0`n', count($data['rows']), $data['table']];
                }

                $doctrine->flush();
                $doctrine->clear();
            }

            $this->dataInsertedOn();
        }
        catch (\Throwable $th)
        {
            $this->dataInsertedOff();
            $messages[] = '`$A problem has been encountered and an error occurred while inserting the data into the database.`0`n';
            $messages[] = $th->getMessage();

            return $messages;
        }

        return $messages;
    }

    /**
     * In an upgrade install use this for insert new data.
     *
     * @return array
     */
    public function makeUpgradeInsertData(): array
    {
        $messages = [];

        $messages[] = '`QNothing to upgrade.`0`n';

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
            $messages[] = '`QThe installation of the modules has been skipped.`0`n';

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
                switch ($op)
                {
                    case 'uninstall':
                        $result = uninstall_module($modulename);
                        $messages[] = ['`3Uninstalling `#%s`0: %s`n', $modulename, $result ? '`@OK!`0' : '`$Failed!`0'];
                    break;
                    case 'install':
                        $result = install_module($modulename);
                        $messages[] = ['`3Installing `#%s`0: %s`n', $modulename, $result ? '`@OK!`0' : '`$Failed!`0'];
                    break;
                    case 'activate':
                        $result = activate_module($modulename);
                        $messages[] = ['`3Activating `#%s`0: %s`n', $modulename, $result ? '`@OK!`0' : '`$Failed!`0'];
                    break;
                    case 'deactivate':
                        $result = deactivate_module($modulename);
                        $messages[] = ['`3Deactivating `#%s`0: %s`n', $modulename, $result ? '`@OK!`0' : '`$Failed!`0'];
                    break;
                    case 'donothing':
                        $messages[] = ['`$Ignoring `#%s`0`n', $modulename];
                    break;
                }
            }
        }

        return $messages;
    }
}
