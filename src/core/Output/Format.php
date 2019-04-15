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

use Lotgd\Core\Pattern as PatternCore;

/**
 * Format a string.
 */
class Format
{
    use PatternCore\Container;
    use PatternCore\Translator;

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
     * @param mixed $indate
     *
     * @return string
     */
    public function relativedate($indate)
    {
        if (! $indate instanceof \DateTime)
        {
            $indate = new \DateTime($indate);
        }

        $now = new \DateTime('now');
        $diff = $now->diff($indate);

        $params = [];
        $sufix = $diff->invert ? 'ago' : 'left';
        $message = "{$sufix}.now";

        if ($diff->y > 0)
        {
            $params['year'] = $diff->y;
            $message = "{$sufix}.year";
        }
        elseif ($diff->m > 0)
        {
            $params['month'] = $diff->m;
            $message = "{$sufix}.month";
        }
        elseif ($diff->d > 0)
        {
            $params['day'] = $diff->d;
            $message = "{$sufix}.day.other";

            if (1 == $diff->d)
            {
                $message = "{$sufix}.day.day";
            }
        }
        elseif ($diff->h > 0)
        {
            $params['hour'] = $diff->h;
            $message = "{$sufix}.hour";
        }
        elseif ($diff->i > 0)
        {
            $params['min'] = $diff->i;
            $message = "{$sufix}.min";
        }

        return $this->getTranslator()->trans($message, $params, 'app-date');
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
