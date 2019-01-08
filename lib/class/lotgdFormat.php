<?php

use Lotgd\Core\Output\Format;

/**
 * This class is for format a string
 * For example: format a number or a date.
 */
class LotgdFormat
{
    /**
     * Instance of Format
     *
     * @var Lotgd\Core\Output\Format
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
     * Set a instance of Lotgd\Core\Output\Format.
     *
     * @param Lotgd\Core\Output\Format $instance
     */
    public static function instance(Format $instance)
    {
        self::$instance = $instance;
    }
}

//-- Configure format instance
LotgdFormat::instance(LotgdLocator::get(\Lotgd\Core\Output\Format::class));
