<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.6.0
 */

namespace Lotgd\Core\Factory\Template;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Lotgd\Core\Template\Params as TemplateParams;

/**
 * Global params for templates.
 */
class Params implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config        = $container->get('GameConfig');
        $opts          = $config['lotgd_core'] ?? [];
        $isDevelopment = ($opts['development'] ?? false);
        $globalParams  = $config['twig_global_params'] ?? [];
        $params        = new TemplateParams();

        foreach($globalParams as $key => $value)
        {
            $params->set($key, $value);
        }

        $params->set('enviroment', $isDevelopment ? 'dev' : 'prod');

        return $params;
    }
}
