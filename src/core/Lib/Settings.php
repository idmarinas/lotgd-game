<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Lib;

use Lotgd\Core\Doctrine\ORM\EntityManager;
use Zend\Cache\Storage\StorageInterface;

class Settings
{
    protected $tablename = 'settings';
    protected $doctrine;
    protected $repository;
    protected $cache;
    protected $settings = [];
    protected $settingsKey = 'game-settings-';

    /**
     * Get a value of a setting.
     *
     * @param string       $settingname
     * @param string|false $default
     *
     * @return string
     */
    public function getSetting($settingname, $default = false)
    {
        if ('usedatacache' == $settingname)
        {
            return true;
        }
        elseif ('datacachepath' == $settingname)
        {
            return $this->cache->getOptions()->getCacheDir();
        }

        if (! is_array($this->settings) || ('object' == gettype($this) && ! isset($this->settings[$settingname])))
        {
            $this->loadSettings();
        }

        if (! isset($this->settings[$settingname]))
        {
            //nothing set, we have to use the default value
            if (file_exists("lib/data/{$this->tablename}.php"))
            {
                require "lib/data/{$this->tablename}.php";
            }

            $setDefault = $default;

            if (false === $default)
            {
                $setDefault = '';

                if (isset($defaults[$settingname]))
                {
                    $setDefault = $defaults[$settingname];
                }
            }

            $this->saveSetting($settingname, $setDefault);

            return $setDefault;
        }

        return $this->settings[$settingname];
    }

    /**
     * Save setting in to Data Base.
     *
     * @param mixed $value
     */
    public function saveSetting(string $settingname, $value): bool
    {
        //-- Not do nothing if not have connection to DB
        if (false === $this->isConnected())
        {
            return false;
        }

        try
        {
            $entity = $this->repository()->find($settingname);
            $entity = $this->repository()->hydrateEntity([
                'setting' => $settingname,
                'value' => $value
            ], $entity);

            $this->doctrine->persist($entity);
            $this->doctrine->flush();
        }
        catch (\Throwable $th)
        {
            return false;
        }

        $this->settings[$settingname] = $value;

        $this->cache->setItem($this->getCacheKey(), $this->settings);

        return true;
    }

    /**
     * Load all settings in table.
     */
    public function loadSettings()
    {
        $this->settings = $this->cache->getItem($this->getCacheKey());

        //-- Not do nothing if not have connection to DB
        if (false === $this->isConnected())
        {
            return null;
        }
        elseif (! is_array($this->settings) || empty($this->settings))
        {
            try
            {
                $this->settings = [];
                $result = $this->repository()->findAll();

                if (! count($result))
                {
                    $this->cache->removeItem($this->getCacheKey());

                    return null;
                }

                foreach ($result as $row)
                {
                    $row = $this->repository()->extractEntity($row);
                    $this->settings[$row['setting']] = $row['value'];
                }

                $this->cache->setItem($this->getCacheKey(), $this->settings);
            }
            catch (\Exception $ex)
            {
                \bdump('Cant get Settings.');

                $this->settings = [];
            }
        }
    }

    /**
     * Force to reload all settings.
     */
    public function clearSettings()
    {
        //scraps the $this->loadSettings() data to force it to reload.
        $this->cache->removeItem($this->getCacheKey());
        $this->settings = [];
        $this->loadSettings();
    }

    /**
     * Get all settings of game.
     *
     * @return array
     */
    public function getAllSettings()
    {
        if (! is_array($this->settings) || empty($this->settings))
        {
            $this->loadSettings();
        }

        return $this->settings;
    }

    /**
     * Alias of getAllSettings().
     *
     * @return array
     */
    public function getArray()
    {
        return $this->getAllSettings();
    }

    /**
     * Set a table name for settings.
     * This table must have a repository.
     */
    public function setTableName(string $table): self
    {
        $this->tablename = $table;

        return $this;
    }

    /**
     * Set doctrine to use.
     *
     * @param Doctrine\ORM\EntityManager
     *
     * @return $this
     */
    public function setDoctrine(EntityManager $doctrine)
    {
        $this->doctrine = $doctrine;

        return $this;
    }

    /**
     * Get repository.
     *
     * @return object|null
     */
    public function repository()
    {
        if (! $this->repository)
        {
            $this->repository = $this->doctrine->getRepository('LotgdCore:'.ucfirst($this->tablename));
        }

        return $this->repository;
    }

    /**
     * Check if have a connection to DB.
     */
    public function isConnected(): bool
    {
        return $this->doctrine->isConnected();
    }

    /**
     * Set cache system.
     *
     * @return $this
     */
    public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Get key of cache.
     */
    protected function getCacheKey(): string
    {
        return $this->settingsKey.$this->tablename;
    }
}
