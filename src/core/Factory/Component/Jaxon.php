<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.2.0
 */

namespace Lotgd\Core\Factory\Component;

use Interop\Container\ContainerInterface;
use Jaxon\Jaxon as JaxonCore;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Jaxon implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $jaxonOptions = $container->get('GameConfig');
        $jaxonOptions = $jaxonOptions['jaxon'] ?? [];

        $jaxon = new JaxonCore();
        $jaxon->di()->getConfig()->setOptions($jaxonOptions);

        //-- Register all class of Lotgd in dir "src/ajax/core"
        $jaxon->register(JaxonCore::CALLABLE_DIR, './src/ajax/core', ['namespace' => 'Lotgd\\Ajax\\Core\\']);

        //-- Register all custom class (Available globally) in dir "src/ajax/local"
        $jaxon->register(JaxonCore::CALLABLE_DIR, './src/ajax/local', ['namespace' => 'Lotgd\\Ajax\\Local\\']);

        $jaxon->plugin('dialog')->registerClasses();

        return $jaxon;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
