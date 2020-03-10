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

use Jaxon\Jaxon as JaxonCore;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\{
    Factory\FactoryInterface,
    ServiceLocatorInterface
};

class Jaxon implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get('GameConfig');
        $jaxonOptions = $options['jaxon'] ?? [];

        $jaxon = new JaxonCore();
        $jaxon->setOptions($jaxonOptions);

        $jaxon->useComposerAutoloader();

        //-- Register all class of Lotgd in dir "src/ajax/core"
        $jaxon->addClassDir('./src/ajax/core', 'Lotgd\\Ajax\\Core\\');
        $jaxon->registerClasses();

        //-- Register all custom class (Available globally) in dir "src/ajax/local"
        $jaxon->addClassDir('./src/ajax/local', 'Lotgd\\Ajax\\Local\\');
        $jaxon->registerClasses();

        $jaxon->plugin('dialog')->registerClasses();

        return $jaxon;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
