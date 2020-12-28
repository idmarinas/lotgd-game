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

\trigger_error(\sprintf(
    'Class %s is deprecated, please use Symfony Translator instead. "$translator = LotgdKernel::get("translator")"',
    Translator::class
), E_USER_DEPRECATED);

/**
 * @deprecated 4.8.0
 */
class Translator
{
    /**
     * Instance of Translator.
     *
     * @var Lotgd\Core\Translator\Translator
     */
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
        \trigger_error('Usage of LotgdTranslator is deprecated, please use "$translator = LotgdKernel::get("translator")" instead', E_USER_DEPRECATED);

        if (\method_exists(self::$container, $method))
        {
            return self::$container->{$method}(...$arguments);
        }
        //-- Is an alias of trans()
        elseif ('translate' == $method || 't' == $method)
        {
            return self::$container->trans(...$arguments);
        }

        $methods = \implode(', ', \get_class_methods(self::$container));

        throw new \BadMethodCallException("Undefined method '{$method}'. The method name must be one of '{$methods}'");
    }

    /**
     * Set container of Translator.
     */
    public static function setContainer(CoreTranslator $container)
    {
        self::$container = $container;
    }
}

\class_alias('Lotgd\Core\Fixed\Translator', 'LotgdTranslator', false);
