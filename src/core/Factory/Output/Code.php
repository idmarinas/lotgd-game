<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Factory\Output;

use Lotgd\Core\Output\Code as OutputCode;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Code implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new OutputCode();
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
