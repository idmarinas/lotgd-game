<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Fixed;

use Lotgd\Core\Kernel as CoreKernel;

class Kernel
{
    /**
     * Intance of Kernel.
     *
     * @var CoreKernel
     */
    protected static $instance;
    protected static $container;

    /**
     * Add support for magic static method calls.
     *
     * @param mixed $method
     * @param array $arguments
     *
     * @return mixed the returned value from the resolved method
     */
    public static function __callStatic($method, $arguments)
    {
        if (\method_exists(self::$instance, $method))
        {
            return self::$instance->{$method}(...$arguments);
        }

        $methods = \implode(', ', \get_class_methods(self::$instance));

        throw new \BadMethodCallException("Undefined method '{$method}'. The method name must be one of '{$methods}'");
    }

    /**
     * Short method for get a service.
     * This replace to LotgdKernel::getContainer()->get('service_name').
     */
    public static function get(string $serviceName)
    {
        if ( ! self::$container)
        {
            self::$container = self::$instance->getContainer();
        }

        return self::$container->get($serviceName);
    }

    /**
     * Set instance of Kernel.
     */
    public static function instance(CoreKernel $instance): void
    {
        self::$instance = $instance;
    }

    /**
     * Get instance of Kernel.
     */
    public static function getInstance()
    {
        return self::$instance;
    }
}

\class_alias('Lotgd\Core\Fixed\Kernel', 'LotgdKernel', false);
