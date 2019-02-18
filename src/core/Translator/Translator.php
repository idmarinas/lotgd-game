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
     * Translate a message of LoTGD.
     *
     * @param string      $message
     * @param array|null  $parameters
     * @param string|null $textDomain
     * @param string|null $locale
     *
     * @return string
     */
    public function trans(string $message, ?array $parameters = [], ?string $textDomain = 'page-default', ?string $locale = null): string
    {
        $locale = ($locale ?: $this->getLocale());
        $parameters = ($parameters ?: []);

        $message = parent::translate($message, $textDomain ?? 'page-default', $locale);

        return $this->mf($message, $parameters, $locale);
    }

    /**
     * Only format a message with MessageFormatter.
     *
     * @param string      $message
     * @param array|null  $parameters
     * @param string|null $locale
     */
    public function mf(string $message, ?array $parameters = [], ?string $locale = null)
    {
        $locale = ($locale ?: $this->getLocale());
        $parameters = ($parameters ?: []);

        $formatter = new \MessageFormatter($locale, $message);

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
