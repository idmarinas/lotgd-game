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
    Factory\FactoryInterface,
    ServiceLocatorInterface
};
use Zend\Validator\AbstractValidator;

class Translator implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        global $session;

        $options = $container->get('GameConfig');
        $translation = $options['lotgd_core']['translation'] ?? [];

        //-- Default language is en
        $language = $translation['locale']['language'] ?? 'en';
        //-- Check if the player has set a language.
        if (($session['user']['loggedin'] ?? false) && ($session['user']['prefs']['language'] ?? false))
        {
            $language = $session['user']['prefs']['language'] ?: 'en';
            $session['user']['prefs']['language'] = $language;
        }

        \Locale::setDefault($language);
        $translator = LotgdTranslator::factory($translation ?? []);
        $translator->setCache($container->get('Cache\Core\Translator'));
        $translator->setPluginManager($container->get(LoaderPluginManager::class));

        //-- Set translator for validators
        AbstractValidator::setDefaultTranslator($translator);
        AbstractValidator::setDefaultTranslatorTextDomain('form-core-validator');

        return $translator;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
