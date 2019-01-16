<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 3.0.0
 */

namespace Lotgd\Core\Fixed;

use Lotgd\Core\Output\Format as CoreFormat;

/**
 * This class is for format a string
 * For example: format a number or a date.
 */
class Format
{
    /**
     * Instance of Format
     *
     * @var Lotgd\Core\Output\CoreFormat
     */
    protected static $instance;

    /**
     * @inheritDoc
     */
    public static function numeral($number, int $decimals = 0, $dec_point = null, $thousands_sep = null)
    {
        return self::$instance->numeral($number, $decimals, $dec_point, $thousands_sep);
    }

    /**
     * @inheritDoc
     */
    public static function relativedate($indate)
    {
        return self::$instance->relativedate($indate);
    }

    /**
     * @inheritDoc
     */
    public static function pluralize($qty, $singular, $plural)
    {
        return self::$instance->pluralize($qty, $singular, $plural);
    }

    /**
     * Set a instance of Lotgd\Core\Output\CoreFormat.
     *
     * @param Lotgd\Core\Output\CoreFormat $instance
     */
    public static function instance(CoreFormat $instance)
    {
        self::$instance = $instance;
    }
}

class_alias('Lotgd\Core\Fixed\Format', 'LotgdFormat', false);
