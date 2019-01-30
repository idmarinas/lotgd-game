<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas (Iván Diaz Marinas) <contacto@infommo.es>
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Translator;

use Zend\I18n\Exception;
use Zend\I18n\Translator\Loader\FileLoaderInterface;
use Zend\I18n\Translator\Translator as ZendTranslator;

/**
 * Class translator for Legend of the Green Dragon.
 * Extends class Zend\I18n\Translator\Translator.
 */
class Translator extends ZendTranslator
{
    /**
     * Translate a message.
     *
     * @param string $message
     * @param array  $parameters
     * @param string $textDomain
     * @param string $locale
     *
     * @return string
     */
    public function trans($message, array $parameters, $textDomain = 'page-default', $locale = null): string
    {
        $message = parent::translate($message, $textDomain, $locale);

        $formatter = new \MessageFormatter($this->getLocale(), $message);

        return $formatter->format($parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function loadMessages($textDomain, $locale)
    {
        if (! isset($this->messages[$textDomain]))
        {
            $this->messages[$textDomain] = [];
        }

        if (null !== ($cache = $this->getCache()))
        {
            $cacheId = $this->getCacheId($textDomain, $locale);

            if (null !== ($result = $cache->getItem($cacheId)))
            {
                $this->messages[$textDomain][$locale] = $result;

                return;
            }
        }

        $messagesLoaded = false;
        $messagesLoaded |= $this->loadLotgdMessagesFromPatterns($textDomain, $locale);
        $messagesLoaded |= $this->loadMessagesFromRemote($textDomain, $locale);
        $messagesLoaded |= $this->loadMessagesFromPatterns($textDomain, $locale);
        $messagesLoaded |= $this->loadMessagesFromFiles($textDomain, $locale);

        if (! $messagesLoaded)
        {
            $discoveredTextDomain = null;

            if ($this->isEventManagerEnabled())
            {
                $until = function ($r)
                {
                    return $r instanceof TextDomain;
                };

                $event = new Event(self::EVENT_NO_MESSAGES_LOADED, $this, [
                    'locale' => $locale,
                    'text_domain' => $textDomain,
                ]);

                $results = $this->getEventManager()->triggerEventUntil($until, $event);

                $last = $results->last();

                if ($last instanceof TextDomain)
                {
                    $discoveredTextDomain = $last;
                }
            }

            $this->messages[$textDomain][$locale] = $discoveredTextDomain;
            $messagesLoaded = true;
        }

        if ($messagesLoaded && null !== $cache)
        {
            $cache->setItem($cacheId, $this->messages[$textDomain][$locale]);
        }
    }

    /**
     * Load messages from LotGD pattern.
     *
     * @param string $namespace
     * @param string $locale
     *
     * @return bool
     *
     * @throws Exception\RuntimeException When specified loader is not a file loader
     */
    protected function loadLotgdMessagesFromPatterns($namespace, $locale)
    {
        $messagesLoaded = false;

        $domains = \explode('-', $namespace);

        if (! ($domains[1] ?? false))
        {
            $domains[1] = $domains[0];
            $domains[0] = 'page';
        }

        $filename = sprintf('data/translations/%s/%s/%s.yaml', $locale, $domains[0], $domains[1]);

        if (is_file($filename))
        {
            $loader = $this->getPluginManager()->get('Yaml');

            if (! $loader instanceof FileLoaderInterface)
            {
                throw new Exception\RuntimeException('Specified loader is not a file loader');
            }

            if (isset($this->messages[$namespace][$locale]))
            {
                $this->messages[$namespace][$locale]->merge($loader->load($locale, $filename));
            }
            else
            {
                $this->messages[$namespace][$locale] = $loader->load($locale, $filename);
            }

            $messagesLoaded = true;
        }

        return $messagesLoaded;
    }
}
