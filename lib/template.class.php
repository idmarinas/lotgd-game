<?php

class LotgdTemplate extends Twig_Environment
{
    protected $twig;
    protected $themename;
    protected $themefolder;
    protected $defaultSkin;

    public function __construct(array $loader = [], array $options = [])
    {
        //-- Merge options
        $default = [
            'cache' => 'cache/templates',
            'autoescape' => false,
            // 'auto_reload' => true
        ];
        $options = array_merge($default, $options);

        $loader = new Twig_Loader_Filesystem($loader);

        parent::__construct($loader, $options);

        //-- Add filters to Twig
        foreach ($this->lotgdFilters() as $filter)
        {
            $this->addFilter($filter);
        }

        //-- Add functions to Twig
        foreach ($this->lotgdFunctions() as $function)
        {
            $this->addFunction($function);
        }
    }

    /**
     * Filters create for LOTGD.
     *
     * @return array
     */
    private function lotgdFilters()
    {
        return [
            //-- Access to appoencode function in template
            new Twig_SimpleFilter('appoencode', function ($string)
            {
                trigger_error(sprintf(
                    'Filter %s is obsolete since 2.6.0; and delete in version 3.0.0 please use "%s" instead',
                    'appoencode',
                    'colorize'
                ), E_USER_DEPRECATED);

                return appoencode($string, true);
            }),
            //-- Alias for appoencode
            new Twig_SimpleFilter('colorize', function ($string)
            {
                return appoencode($string, true);
            }),
            //-- Access to color_sanitize function in template
            new Twig_SimpleFilter('color_sanitize', function ($string)
            {
                trigger_error(sprintf(
                    'Filter %s is obsolete since 2.6.0; and delete in version 3.0.0 please use "%s" instead',
                    'color_sanitize',
                    'uncolorize'
                ), E_USER_DEPRECATED);

                return color_sanitize($string);
            }),
            //-- Alias for color_sanitize
            new Twig_SimpleFilter('uncolorize', function ($string)
            {
                return color_sanitize($string);
            }),
            //-- Add a link, but not nav
            new Twig_SimpleFilter('lotgd_url', function ($url)
            {
                addnav('', $url);

                return $url;
            }),
            //-- Create a link popup
            new Twig_SimpleFilter('lotgd_popup', function ($url)
            {
                return popup($url);
            }),
            new Twig_SimpleFilter('nltoappon', function ($string)
            {
                require_once 'lib/nltoappon.php';

                return nltoappon($string);
            }),
            /*
             * Format a number
             *
             * @param float $number
             * @param int $decimals
             *
             * @return string
             */
            new Twig_SimpleFilter('numeral', function ($number, $decimals = 0)
            {
                global $lotgdFormat;

                return $lotgdFormat->numeral($number, $decimals);
            }),
            //-- Translate a text in template
            new Twig_SimpleFilter('t', function ($data, $namespace = false)
            {
                if (is_array($data))
                {
                    $text = str_replace('`%', '`%%', $data[0]);
                    unset($data[0]);

                    return vsprintf(translate_inline($text, $namespace), $data);
                }
                else
                {
                    return translate_inline($data, $namespace);
                }
            }),
            //-- Show a relative date from now
            new Twig_SimpleFilter('relativedate', function ($string)
            {
                global $lotgdFormat;

                return $lotgdFormat->relativedate($string);
            }),
            //-- Search and replace keywords
            new Twig_SimpleFilter('sustitute', function ($string)
            {
                global $output;

                return $output->sustitute((string) $string);
            })
        ];
    }

    private function lotgdFunctions()
    {
        return [
            new Twig_SimpleFunction('modulehook', function ($name, $data)
            {
                return modulehook($name, $data);
            }),
            new Twig_SimpleFunction('isValidProtocol', function ($url)
            {
                // We should check all legeal protocols
                $protocols = ['http', 'https', 'ftp', 'ftps'];
                $protocol = explode(':', $url, 2);
                $protocol = $protocol[0];

                // This will take care of download strings such as: not publically released or contact admin
                return in_array($protocol, $protocols);
            }),
            //-- Get value of setting
            new Twig_SimpleFunction('getsetting', function ($name, $default)
            {
                return getsetting($name, $default);
            }),
            //-- Time in the game
            new Twig_SimpleFunction('gametime', function ()
            {
                return getgametime();
            }),
            //-- Seconds to next game day
            new Twig_SimpleFunction('secondstonextgameday', function ()
            {
                return secondstonextgameday();
            }),
        ];
    }
}
