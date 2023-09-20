<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Fixed;

use BadMethodCallException;

/**
 * @method static string t($id, array $parameters = [], $domain = null, $locale = null)
 * @method static string trans($id, array $parameters = [], $domain = null, $locale = null)
 */
class Translator
{
    use StaticTrait;

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
        //-- Is an alias of trans()
        elseif ('translate' == $method || 't' == $method)
        {
            return self::$instance->trans(...$arguments);
        }

        $methods = \implode(', ', \get_class_methods(self::$instance));

        throw new BadMethodCallException("Undefined method '{$method}'. The method name must be one of '{$methods}'");
    }
}

\class_alias('Lotgd\Core\Fixed\Translator', 'LotgdTranslator', false);
