<?php

/**
 * This class is for format a string
 * For example: format a number or a date.
 */
class LotgdFormat
{
    protected static $dec_point;
    protected static $thousands_sep;

    /**
     * Format a number.
     *
     * @param float|int $number
     * @param int       $decimals
     * @param string    $point
     * @param string    $sep
     *
     * @return number
     */
    public static function numeral($number, int $decimals = 0, $dec_point = null, $thousands_sep = null)
    {
        $number = (float) $number;
        $decimals = (int) $decimals;

        //-- Check if use default value or custom
        $dec_point = $dec_point ?: self::$dec_point;
        $thousands_sep = $thousands_sep ?: self::$thousands_sep;

        //-- If decimals is negative, it is automatically determined
        if ($decimals < 0)
        {
            $decimals = strlen(substr(strrchr($number, '.'), 1));
        }

        //-- This avoid decimals with only ceros
        $number = (float) round($number, $decimals);
        //-- Count the decimals again
        $decimals = strlen(substr(strrchr($number, '.'), 1));

        return number_format($number, $decimals, $dec_point, $thousands_sep);
    }

    /**
     * Show a relative date.
     *
     * @param string $indate
     *
     * @return string
     */
    public static function relativedate($indate)
    {
        $laston = is_numeric($indate) ? $indate : strtotime($indate);
        $laston = round((time() - strtotime($indate)) / 86400, 0).' days';

        if ('1 ' == substr($laston, 0, 2))
        {
            $laston = '1 day';
        }
        elseif (date('Y-m-d', strtotime($laston)) == date('Y-m-d'))
        {
            $laston = 'Today';
        }
        elseif (date('Y-m-d', strtotime($laston)) == date('Y-m-d', strtotime('-1 day')))
        {
            $laston = 'Yesterday';
        }
        elseif (false !== strpos($indate, '0000-00-00'))
        {
            $laston = 'Never';
        }
        else
        {
            $laston = ['%s days', round((time() - strtotime($indate)) / 86400, 0)];
        }

        tlschema('datetime');
        $laston = sprintf_translate($laston);
        tlschema();

        return $laston;
    }

    /**
     * Select the plural or singular form according to the past number.
     *
     * @param int    $qty      Quantity to determine form
     * @param string $singular Singular word
     * @param string $plural   Plural word
     *
     * @return string
     */
    public static function pluralize($qty, $singular, $plural)
    {
        return 1 == $qty ? $singular : $plural;
    }

    /**
     * Set decimal point.
     *
     * @param string $val
     */
    public static function setDecPoint(string $val)
    {
        self::$dec_point = $val;
    }

    /**
     * Set thousands separation.
     *
     * @param string $val
     */
    public static function setThousandsSep(string $val)
    {
        self::$thousands_sep = $val;
    }
}

//-- Configure money decimal and thousands
LotgdFormat::setDecPoint(getsetting('moneydecimalpoint', '.'));
LotgdFormat::setThousandsSep(getsetting('moneythousandssep', ','));
