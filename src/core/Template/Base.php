<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Template;

use Twig_Environment;
use Twig_Filter;
use Twig_Function;
use Twig_Loader_Filesystem;

class Base extends Twig_Environment
{
    use \Lotgd\Core\Pattern\Container;

    public function __construct(array $loader = [], array $options = [])
    {
        //-- Merge options
        $default = [
            'cache' => 'cache/templates',
            'autoescape' => false
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
     * Filters created for LotGD.
     *
     * @return array
     */
    private function lotgdFilters(): array
    {
        return [
            //-- Access to appoencode function in template
            new Twig_Filter('colorize', function (string $string)
            {
                return appoencode($string, true);
            }),
            //-- Access to color_sanitize function in template
            new Twig_Filter('uncolorize', function (string $string)
            {
                return color_sanitize($string);
            }),
            //-- Add a link, but not nav
            new Twig_Filter('lotgd_url', function ($url)
            {
                addnav('', $url);

                return $url;
            }),
            new Twig_Filter('nltoappon', function ($string)
            {
                require_once 'lib/nltoappon.php';

                return nltoappon($string);
            }),
            //-- Format a number
            new Twig_Filter('numeral', function ($number, $decimals = 0)
            {
                return \LotgdFormat::numeral($number, $decimals);
            }),
            //-- Show a relative date from now
            new Twig_Filter('relativedate', function ($string)
            {
                return \LotgdFormat::relativedate($string);
            }),
            //-- Search and replace keywords
            new Twig_Filter('sustitute', function ($string)
            {
                global $output;

                return $output->sustitute((string) $string);
            })
        ];
    }

    /**
     * Functions created for LotGD.
     *
     * @return array
     */
    private function lotgdFunctions(): array
    {
        return [
            new Twig_Function('modulehook', function ($name, $data)
            {
                return modulehook($name, $data);
            }),
            new Twig_Function('isValidProtocol', function ($url)
            {
                // We should check all legeal protocols
                $protocols = ['http', 'https', 'ftp', 'ftps'];
                $protocol = explode(':', $url, 2);
                $protocol = $protocol[0];

                // This will take care of download strings such as: not publically released or contact admin
                return in_array($protocol, $protocols);
            }),
            //-- Get value of setting
            new Twig_Function('getsetting', function ($name, $default)
            {
                return getsetting($name, $default);
            }),
            //-- Time in the game
            new Twig_Function('gametime', function ()
            {
                return getgametime();
            }),
            //-- Seconds to next game day
            new Twig_Function('secondstonextgameday', function ()
            {
                return secondstonextgameday();
            }),
        ];
    }
}
