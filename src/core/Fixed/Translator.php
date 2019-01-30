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
     * @see Lotgd\Core\Component\Translator
     */
    public static function trans($message, array $parameters, $textDomain = 'page-default', $locale = null)
    {
        return self::$container->trans($message, $parameters, $textDomain, $locale);
    }

    /**
     * Is an alias of trans()
     */
    public static function translate($message, array $parameters, $textDomain = 'page-default', $locale = null)
    {
        return self::$container->trans($message, $parameters, $textDomain, $locale);
    }

    /**
     * Is an alias of trans()
     */
    public static function t($message, array $parameters, $textDomain = 'page-default', $locale = null)
    {
        return self::$container->trans($message, $parameters, $textDomain, $locale);
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
