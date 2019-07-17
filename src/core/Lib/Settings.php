<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Lib;

use Lotgd\Core\Lib\Cache;
use Doctrine\ORM\EntityManager;

class Settings
{
    protected $tablename;
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
            return $this->cache->isActive();
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
     * @param string $settingname
     * @param mixed  $value
     *
     * @return bool
     */
    public function saveSetting(string $settingname, $value): bool
    {
        //-- Not do nothing if not have connection to DB
        if (false === $this->isConnected())
        {
            return false;
        }

        $entity = $this->repository()->find($settingname);
        $entity = $this->repository()->hydrateEntity([
            'setting' => $settingname,
            'value' => $value
        ], $entity);

        $this->repository()->persist($entity);
        $this->repository()->flush();

        $this->settings[$settingname] = $value;

        $this->cache->updateData($this->getCacheKey(), $this->settings, true);

        return true;
    }

    /**
     * Load all settings in table.
     */
    public function loadSettings()
    {
        $this->settings = $this->cache->getData($this->getCacheKey(), 86400, true);

        //-- Not do nothing if not have connection to DB
        if (false === $this->isConnected())
        {
            return;
        }
        elseif (! is_array($this->settings) || empty($this->settings))
        {
            try
            {
                $this->settings = [];
                $result = $this->repository()->findAll();

                if (! count($result))
                {
                    return;
                }

                foreach ($result as $row)
                {
                    $row = $this->repository()->extractEntity($row);
                    $this->settings[$row['setting']] = $row['value'];
                }

                $this->cache->updateData($this->getCacheKey(), $this->settings, true);
            }
            catch (\Exception $ex)
            {
                debug('Cant get Settings.');
            }
        }
    }

    /**
     * Force to reload all settings.
     */
    public function clearSettings()
    {
        //scraps the $this->loadSettings() data to force it to reload.
        $this->cache->invalidateData($this->getCacheKey(), true);
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
            $this->repository = $this->doctrine->getRepository('LotgdCore:Settings');
        }

        return $this->repository;
    }

    /**
     * Check if have a connection to DB.
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->doctrine->getConnection()->isConnected();
    }

    /**
     * Set cache system
     *
     * @return $this
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Get key of cache.
     *
     * @return string
     */
    protected function getCacheKey(): string
    {
        return $this->settingsKey.$this->tablename;
    }
}
