<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Patern;

use Interop\Container\ContainerInterface;

trait Container
{
    protected $serviceManager;

    /**
     * Set container (Service Manager).
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->serviceManager = $container;

        return $this;
    }

    /**
     * Get container.
     *
     * @param string $name
     *
     * @return object
     */
    public function getContainer($name = null)
    {
        if (! $name)
        {
            return $this->serviceManager;
        }
        else
        {
            return $this->serviceManager->get($name);
        }
    }
}
