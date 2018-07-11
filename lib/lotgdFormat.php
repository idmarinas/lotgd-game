<?php

/**
 * This class is for format a string
 * For example: format a number or a date.
 */
class LotgdFormat
{
    protected $dec_point;
    protected $thousands_sep;

    public function __construct()
    {
        $this->dec_point = getsetting('moneydecimalpoint', '.');
        $this->thousands_sep = getsetting('moneythousandssep', ',');
    }

    /**
     * Format a number.
     *
     * @param float  $number
     * @param int    $decimals
     * @param string $point
     * @param string $sep
     *
     * @return number
     */
    public function numeral($number, $decimals = 0, $dec_point = false, $thousands_sep = false)
    {
        $number = (float) $number;
        $decimals = (int) $decimals;

        //-- Check if use default value or custom
        $dec_point = $dec_point ?: $this->dec_point;
        $thousands_sep = $thousands_sep ?: $this->thousands_sep;

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
     * Undocumented function.
     *
     * @param string $indate
     *
     * @return string|array
     */
    public function relativedate($indate)
    {
        $laston = is_numeric($indate) ? $indate : strtotime($indate);
        $laston = round((time() - strtotime($indate)) / 86400, 0).' days';

        tlschema('datetime');

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

        tlschema();

        return $laston;
    }
}

global $lotgdFormat;

$lotgdFormat = new LotgdFormat();
