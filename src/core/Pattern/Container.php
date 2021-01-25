<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Pattern;

use Lotgd\Core\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface as DependencyInjectionContainerInterface;

trigger_error(Container::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
trait Container
{
    protected $lotgdKernel;
    protected $lotgdKernelContainer;

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
