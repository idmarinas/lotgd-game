<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Lib;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class Settings
{
    protected $tablename = 'settings';
    protected $doctrine;
    protected $repository;
    protected $cache;
    protected $settings    = [];
    protected $settingsKey = 'game-settings-';

    public function __construct(CacheInterface $cache, EntityManagerInterface $doctrine)
    {
        $this->cache    = $cache;
        $this->doctrine = $doctrine;
    }

    /**
     * Get a value of a setting.
     *
     * @param string       $settingname
     * @param string|false $default
     *
     * @return string
     */
    public function getSetting($settingname, $default = null)
    {
        if ('usedatacache' == $settingname)
        {
            return true;
        }
        elseif ('datacachepath' == $settingname)
        {
            return '"var/cache"';
        }

        $this->loadSettings();

        if ( ! isset($this->settings[$settingname]))
        {
            $this->saveSetting($settingname, $default);
        }

        return $this->settings[$settingname] ?? $default;
    }

    /**
     * Save setting in to Data Base.
     *
     * @param mixed $value
     */
    public function saveSetting(string $settingname, $value): bool
    {
        try
        {
            $entity = $this->repository()->find($settingname);
            $entity = $this->repository()->hydrateEntity([
                'setting' => $settingname,
                'value'   => $value,
            ], $entity);

            $this->doctrine->persist($entity);
            $this->doctrine->flush();
        }
        catch (\Throwable $th)
        {
            return false;
        }

        $this->loadSettings();

        $this->settings[$settingname] = $value;

        $item = $this->cache->getItem($this->getCacheKey());
        $item->set($this->settings);
        $this->cache->save($item);

        return true;
    }

    /**
     * Load all settings in table.
     */
    public function loadSettings(): void
    {
        $this->settings = $this->cache->get($this->getCacheKey(), function (ItemInterface $item)
        {
            $item->expiresAt(new \DateTime('tomorrow'));

            $sets = [];

            try
            {
                $result = $this->repository()->findAll();

                foreach ($result as $row)
                {
                    $sets[$row->getSetting()] = $row->getValue();
                }
            }
            catch (\Exception $ex)
            {
                \Tracy\Debugger::log($ex);
            }

            //-- If not found mark as expired
            if (empty($sets))
            {
                $item->expiresAt(new \DateTime('now'));
            }

            return $sets;
        });
    }

    /**
     * Force to reload all settings.
     */
    public function clearSettings()
    {
        //scraps the $this->loadSettings() data to force it to reload.
        $this->cache->delete($this->getCacheKey());
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
        $this->loadSettings();

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
     * Get repository.
     *
     * @return object|null
     */
    public function repository()
    {
        if ( ! $this->repository)
        {
            $this->repository = $this->doctrine->getRepository('LotgdCore:'.ucfirst($this->tablename));
        }

        return $this->repository;
    }

    /**
     * Get key of cache.
     */
    protected function getCacheKey(): string
    {
        return $this->settingsKey.$this->tablename;
    }
}
