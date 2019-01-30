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
            'cache' => 'data/cache/templates',
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
            //-- Search and replace keywords
            new Twig_Filter('sustitute', function ($string)
            {
                global $output;

                trigger_error(sprintf(
                    'Usage of %s filter is obsolete since 4.0.0; and delete in version 4.1.0, use new template system to simulate this.',
                    __METHOD__
                ), E_USER_DEPRECATED);

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
        return [];
    }
}
