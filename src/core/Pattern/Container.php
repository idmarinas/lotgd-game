<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Pattern;

use Interop\Container\ContainerInterface;
use Lotgd\Core\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface as DependencyInjectionContainerInterface;

trait Container
{
    protected $serviceManager;
    protected $lotgdKernel;
    protected $lotgdKernelContainer;

    /**
     * Set container (Service Manager).
     *
     * @deprecated 4.10.0
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
     *
     * @deprecated 4.10.0
     */
    public function getContainer($name = null)
    {
        if ( ! $name)
        {
            return $this->serviceManager;
        }

        //-- TEMP: avoid error when not configure instance of service manager
        if ( ! $this->serviceManager instanceof ContainerInterface)
        {
            return \LotgdLocator::get($name);
        }

        return $this->serviceManager->get($name);
    }

    /**
     * Get container.
     *
     * @param string $name
     */
    public function getService($name)
    {
        if ( ! $this->lotgdKernelContainer instanceof DependencyInjectionContainerInterface)
        {
            $this->lotgdKernelContainer = $this->getKernel()->getContainer();
        }

        return $this->lotgdKernelContainer->get($name);
    }

    /**
     * Get LoTGD Kernel.
     */
    public function getKernel(): Kernel
    {
        if ( ! $this->lotgdKernel instanceof Kernel)
        {
            $this->lotgdKernel = \LotgdKernel::getInstance();
        }

        return $this->lotgdKernel;
    }
}
