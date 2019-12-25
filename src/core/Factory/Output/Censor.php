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

namespace Lotgd\Core\Factory\Output;

use Interop\Container\ContainerInterface;
use Lotgd\Core\Output\Censor as OutputCensor;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Censor implements FactoryInterface
{
    const LOTGD_DICTIONARY_PATH = 'data/dictionary';

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $language = \Locale::getDefault();
        $profanity = new OutputCensor();
        $profanity->addDictionary(self::LOTGD_DICTIONARY_PATH . '/en.php');//-- Custom dictionary

        if ('en' != $language)
        {
            $profanity->addDictionary($language);
            $customLanguage = self::LOTGD_DICTIONARY_PATH . "/{$language}.php";
            if (file_exists($customLanguage))
            {
                $profanity->addDictionary($customLanguage);
            }
        }

        return $profanity;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
