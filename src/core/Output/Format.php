<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Output;

/**
 * Format a string.
 */
class Format
{
    /**
     * Style of decimal point.
     *
     * @var string
     */
    protected $dec_point;

    /**
     * Style of thousands point.
     *
     * @var string
     */
    protected $thousands_sep;

    /**
     * Format a number.
     *
     * @param float|int $number
     * @param int       $decimals
     * @param string    $point
     * @param string    $sep
     *
     * @return string
     */
    public function numeral($number, int $decimals = 0, string $dec_point = null, string $thousands_sep = null)
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
     * Show a relative date.
     *
     * @param string|int $indate
     *
     * @return string
     */
    public function relativedate($indate)
    {
        if ($indate instanceof \DateTime)
        {
            $indate = $indate->getTimestamp();
        }

        $indate = is_numeric($indate) ? $indate : strtotime($indate);
        $lastoncheck = round((time() - strtotime($indate)) / 86400, 0).' days';

        $laston = ['%s days', round((time() - strtotime($indate)) / 86400, 0)];

        if ('1 ' == substr($lastoncheck, 0, 2))
        {
            $laston = '1 day';
        }
        elseif (date('Y-m-d', strtotime($lastoncheck)) == date('Y-m-d'))
        {
            $laston = 'Today';
        }
        elseif (date('Y-m-d', strtotime($lastoncheck)) == date('Y-m-d', strtotime('-1 day')))
        {
            $laston = 'Yesterday';
        }
        elseif (false !== strpos($lastoncheck, '0000-00-00'))
        {
            $laston = 'Never';
        }

        tlschema('datetime');
        $laston = sprintf_translate($laston);
        tlschema();

        return $laston;
    }

    /**
     * Set decimal point.
     *
     * @param string $val
     */
    public function setDecPoint(string $val)
    {
        $this->dec_point = $val;
    }

    /**
     * Set thousands separation.
     *
     * @param string $val
     */
    public function setThousandsSep(string $val)
    {
        $this->thousands_sep = $val;
    }
}
