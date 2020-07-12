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

namespace Lotgd\Core\Factory\Translator;

use Gedmo\Translatable\TranslatableListener as TranslatorListener;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\{
    Factory\FactoryInterface,
    ServiceLocatorInterface
};

class TranslatableListener implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $listener = new TranslatorListener();
        $listener->setDefaultLocale('en');
        $listener->setTranslationFallback(true);
        $listener->setPersistDefaultLocaleTranslation(false);

        return $listener;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
