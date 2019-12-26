<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Template;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Base extends Environment
{
    use \Lotgd\Core\Pattern\Container;

    public function __construct(array $loader = [], array $options = [])
    {
        //-- Merge options
        $default = [
            'cache' => 'storage/cache/templates',
            'autoescape' => false
        ];
        $options = array_merge($default, $options);

        $loader = new FilesystemLoader($loader);

        parent::__construct($loader, $options);
    }
}
