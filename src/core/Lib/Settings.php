<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Lib;

use Lotgd\Core\Lib\Cache;
use Lotgd\Core\Lib\Dbwrapper;

class Settings
{
    protected $tablename;
    protected $wrapper;
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

            if (false === $default)
            {
                if (isset($defaults[$settingname]))
                {
                    $setDefault = $defaults[$settingname];
                }
                else
                {
                    $setDefault = '';
                }
            }
            else
            {
                $setDefault = $default;
            }

            $this->saveSetting($settingname, $setDefault);

            return $setDefault;
        }
        else
        {
            return $this->settings[$settingname];
        }
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
        $settings = $this->getAllSettings();

        //-- To ensure that a new record is inserted or the existing one is updated
        $sql = sprintf(
            'REPLACE INTO `%s` (`setting`, `value`) VALUES (%s, %s)',
            $this->tablename,
            $this->wrapper->quoteValue($settingname),
            $this->wrapper->quoteValue($value)
        );
        $this->wrapper->query($sql);

        $settings[$settingname] = $value;

        $this->cache->updateData($this->getCacheKey(), $settings, true);

        $this->settings = $settings;

        if ($this->wrapper->getAffectedRows() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Load all settings in table.
     */
    public function loadSettings()
    {
        $this->settings = $this->cache->getData($this->getCacheKey(), 86400, true);

        if (! is_array($this->settings) || empty($this->settings))
        {
            try
            {
                $this->settings = [];
                $select = $this->wrapper->select($this->tablename);
                $result = $this->wrapper->execute($select);

                if (! $result->count())
                {
                    return;
                }

                foreach ($result as $row)
                {
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
     * Set table name
     *
     * @param string $tablename
     *
     * @return $this
     */
    public function setTableName(string $tablename = '')
    {

        if ($tablename)
        {
            $tablename = $this->wrapper->prefix($tablename);
        }
        else
        {
            $tablename = $this->wrapper->prefix('settings');
        }

        $this->tablename = $tablename;

        return $this;
    }

    /**
     * Set wrapper to use
     *
     * @return $this
     */
    public function setWrapper(Dbwrapper $wrapper)
    {
        $this->wrapper = $wrapper;

        return $this;
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
