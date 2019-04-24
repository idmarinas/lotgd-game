<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Fixed;

use Lotgd\Core\Translator\Translator as CoreTranslator;

class Translator
{
    /**
     * Instance of Translator
     *
     * @var Lotgd\Core\Translator\Translator
     */
    protected static $container;

    /**
     * Add support for magic static method calls.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed the returned value from the resolved method
     */
    public static function __callStatic($method, $arguments)
    {
        if (\method_exists(self::$container, $method))
        {
            return self::$container->{$method}(...$arguments);
        }
        //-- Is an alias of trans()
        elseif ('translate' == $method || 't' == $method)
        {
            return self::$container->trans(...$arguments);
        }

        $methods = implode(', ', get_class_methods(self::$container));

        throw new \BadMethodCallException("Undefined method '$method'. The method name must be one of '$methods'");
    }

    /**
     * Set container of Translator
     *
     * @param CoreTranslator $container
     */
    public static function setContainer(CoreTranslator $container)
    {
        self::$container = $container;
    }
}

class_alias('Lotgd\Core\Fixed\Translator', 'LotgdTranslator', false);
