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

use function class_alias;

class Kernel
{
    use StaticTrait;

    protected static $container;

    /**
     * Short method for get a service.
     * This replaces to LotgdKernel::getContainer()->get('service_name').
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
     * Get instance of Kernel.
     */
    public static function getInstance()
    {
        return self::$instance;
    }
}

class_alias('Lotgd\Core\Fixed\Kernel', 'LotgdKernel', false);
