<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Lib;

use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\Plugin\ExceptionHandler;
use Zend\Cache\Storage\Plugin\PluginOptions;
use Zend\Cache\Storage\Plugin\Serializer;

class Cache extends Filesystem
{
    protected $cacheActivated;

    public function __construct(array $options = [])
    {
        $default = [
            'key_pattern' => '/^[a-z0-9_\+\-\/\.]*$/Di'
        ];

        $options = array_merge($default, $options);

        parent::__construct($options);

        //-- Add plugins to cache system
        $this->addPlugin(new Serializer());

        $plugin = new ExceptionHandler();
        $plugin->setOptions(new PluginOptions(['throw_exceptions' => false]));
        $this->addPlugin($plugin);
    }

    /**
     * Get a storage cache data.
     *
     * @param string $name
     * @param int $duration
     * @param bool $force
     *
     * @return mixed
     */
    public function getData(string $name, int $duration = 120, bool $force = false)
    {
        if (false === $this->isActive() && false === $force)
        {
            return;
        }

        //-- Set Duration
        $duration = is_numeric($duration) ? max($duration, 30) : 120;//-- Min duration is 30 seg
        $this->getOptions()->setTtl($duration);

        return $this->getItem($name);
    }

    /**
     * Set data cache.
     *
     * @param string $name  Key for a data storage
     * @param mixed    $data  Data to cache
     * @param bool $force Force to update cache
     *
     * @return bool
     */
    function updateData(string $name, $data, bool $force = false)
    {
        if (false === $this->isActive() && false === $force)
        {
            return;
        }

        return $this->setItem($name, $data);
    }

    /**
     * We want to be able to invalidate data caches when we know we've done
     * something which would change the data.
     *
     * @param string $name  Key for a data storage
     * @param bool   $force Force to invalidate cache
     *
     * @return bool
     */
    function invalidateData(string $name, bool $force = false)
    {
        if (false === $this->isActive() && false === $force)
        {
            return;
        }

        return $this->removeItem($name);
    }

    /**
     * Invalidates *all* caches, which $prefix of their filename.
     *
     * @param string $prefix Prefix to invalidate
     * @param bool   $force  Force to remove cache
     *
     * @return bool
     */
    function massInvalidate(string $prefix, bool $force = false)
    {
        if (false === $this->isActive() && false === $force)
        {
            return;
        }

        return $this->clearByPrefix($prefix);
    }

    /**
     * Flush the whole storage.
     *
     * @return bool
     */
    function dataEmpty()
    {
        try
        {
            $result = $this->flush();
        }
        catch (\Exception $ex)
        {
            //-- With this avoid a 500 server error
            //-- In some cases it may not be possible to delete certain files and directories because it not have permissions.
            $result = true;
        }

        return $result;
    }

    /**
     * Remove expired data cache.
     *
     * @return bool
     */
    function dataClearExpired()
    {
        return $this->clearExpired();
    }

    /**
     * Optimize the storage.
     *
     * @return bool
     */
    function dataOptimize()
    {
        return $this->optimize();
    }

    /**
     * Set if cache system is active
     *
     * @param bool $active
     *
     * @return void
     */
    public function setActive(bool $active)
    {
        $this->cacheActivated = $active;

        return $this;
    }

    /**
     * Check if cache system is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool) $this->cacheActivated;
    }
}
