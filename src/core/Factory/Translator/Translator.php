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

namespace Lotgd\Core\Factory\Translator;

use Interop\Container\ContainerInterface;
use Lotgd\Core\Translator\Translator as LotgdTranslator;
use Zend\I18n\Translator\LoaderPluginManager;
use Zend\ServiceManager\{
    FactoryInterface,
    ServiceLocatorInterface
};
class Translator implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get('GameConfig');

        $translator = LotgdTranslator::factory($options['lotgd_core']['translation'] ?? []);
        $translator->setPluginManager($container->get(LoaderPluginManager::class));

        return $translator;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
