<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Output;

use DateTime;
use MessageFormatter;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Format a string.
 */
class Format
{
    use Pattern\Code;
    use Pattern\Color;

    protected $codes;
    protected $colors;
    protected $translator;

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

    public function __construct(TranslatorInterface $translator, Code $code, Color $colors)
    {
        $this->translator = $translator;
        $this->codes      = $code;
        $this->colors     = $colors;
    }

    /**
     * Format a number.
     *
     * @param float|int $number
     * @param string    $dec_point
     * @param string    $thousands_sep
     *
     * @return string
     */
    public function numeral($number, int $decimals = 0, ?string $dec_point = null, ?string $thousands_sep = null)
    {
        $number   = (float) $number;

        //-- Check if use default value or custom
        $dec_point     = $dec_point ?: $this->dec_point;
        $thousands_sep = $thousands_sep ?: $this->thousands_sep;

        //-- If decimals is negative, it is automatically determined
        if ($decimals < 0)
        {
            $decimals = \strlen(\substr(\strrchr($number, '.'), 1));
        }

        //-- This avoid decimals with only ceros
        $number = \round($number, $decimals);
        //-- Count the decimals again
        $decimals = \strlen(\substr(\strrchr($number, '.'), 1));

        return \number_format($number, $decimals, $dec_point, $thousands_sep);
    }

    /**
     * Show a relative date.
     *
     * @param mixed  $indate
     * @param string $default
     */
    public function relativedate($indate, $default = 'never'): string
    {
        if ( ! $indate instanceof DateTime)
        {
            $indate = new DateTime($indate);
        }
        $nullDate = new DateTime('0000-00-00 00:00:00');
        $default  = $default ?: 'never';

        if ($nullDate == $indate)
        {
            return $this->translator->trans($default, [], 'app_date');
        }

        $now  = new DateTime('now');
        $diff = $now->diff($indate);

        $params  = [];
        $sufix   = $diff->invert !== 0 ? 'ago' : 'left';
        $message = "{$sufix}.now";

        if ($diff->y > 0)
        {
            $params['year'] = $diff->y;
            $message        = "{$sufix}.year";
        }
        elseif ($diff->m > 0)
        {
            $params['month'] = $diff->m;
            $message         = "{$sufix}.month";
        }
        elseif ($diff->d > 0)
        {
            $params['day'] = $diff->d;
            $message       = "{$sufix}.day.other";

            if (1 == $diff->d)
            {
                $message = "{$sufix}.day.day";
            }
        }
        elseif ($diff->h > 0)
        {
            $params['hour'] = $diff->h;
            $message        = "{$sufix}.hour";
        }
        elseif ($diff->i > 0)
        {
            $params['min'] = $diff->i;
            $message       = "{$sufix}.min";
        }

        return $this->translator->trans($message, $params, 'app_date');
    }

    /**
     * This function puts the lotgd formatting `whatever into HTML tags.
     *
     * @param string $string the LoTGD formatted string
     */
    public function colorize(string $string)
    {
        $patternOpen     = $this->getColorPatternOpen();
        $patternClose    = $this->getColorPatternClose();
        $replacementOpen = $this->getColorReplacementOpen();

        $string = \str_replace($patternOpen, $replacementOpen, $string);
        $string = \str_replace($patternClose, '</span>', $string);

        //-- Replace codes of string
        $patternOpen      = $this->getCodePatternOpen();
        $patternClose     = $this->getCodePatternClose();
        $replacementOpen  = $this->getCodeReplacementOpen();
        $replacementClose = $this->getCodeReplacementClose();

        $string = \str_replace($patternOpen, $replacementOpen, $string);
        $string = \str_replace($patternClose, $replacementClose, $string);

        //-- Special codes
        $patternOpen      = $this->getCodeSpecialPatternOpen();
        $patternClose     = $this->getCodeSpecialPatternClose();
        $replacementOpen  = $this->getCodeSpecialReplacementOpen();
        $replacementClose = $this->getCodeSpecialReplacementClose();

        $string = \str_replace($patternOpen, $replacementOpen, $string);

        return \str_replace($patternClose, $replacementClose, $string);
    }

    /**
     * Full sanitize a string, removed color and codes.
     * All LoTGD codes included (`i `b `c) and others.
     */
    public function uncolorize(string $string): string
    {
        return \preg_replace('/[`´]./u', '', $string);
    }

    /**
     * Set decimal point.
     */
    public function setDecPoint(string $val)
    {
        $this->dec_point = $val;
    }

    /**
     * Set thousands separation.
     */
    public function setThousandsSep(string $val)
    {
        $this->thousands_sep = $val;
    }

    /**
     * Only format a message with MessageFormatter.
     */
    public function messageFormatter(string $message, ?array $parameters = [], ?string $locale = null): string
    {
        //-- Not do nothing if message is empty
        //-- MessageFormatter fail if message is empty
        if ('' == $message)
        {
            return '';
        }

        $locale     = ($locale ?: $this->translator->getLocale());
        $parameters = ($parameters ?: []);
        //-- Delete all values that not are allowed (can cause a error when use \MessageFormatter::format($params))
        $parameters = \array_filter($parameters, [$this, 'cleanParameters']);

        $formatter = new MessageFormatter($locale, $message);

        return $formatter->format($parameters);
    }

    /** alias */
    public function mf(string $message, ?array $parameters = [], ?string $locale = null): string
    {
        return $this->messageFormatter($message, $parameters, $locale);
    }

    /**
     * Clean param of a value.
     *
     * @param mixed $param
     */
    private function cleanParameters($param): bool
    {
        return \is_string($param) //-- Allow string values
        || \is_numeric($param) //-- Allow numeric values
        || \is_bool($param) //-- Allow bool values
        || \is_null($param) //-- Allow null value (Formatter can handle this value)
        || $param instanceof DateTime;
    }
}
