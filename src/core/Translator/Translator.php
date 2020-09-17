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

use Laminas\EventManager\Event;
use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Loader\FileLoaderInterface;
use Laminas\I18n\Translator\Translator as ZendTranslator;
use Laminas\Validator\Translator\TranslatorInterface;

/**
 * Class translator for Legend of the Green Dragon.
 * Extends class Laminas\I18n\Translator\Translator.
 */
class Translator extends ZendTranslator implements TranslatorInterface
{
    const TEXT_DOMAIN_DEFAULT = 'page-default';

    /**
     * Translate a message of LoTGD WITH MessageFormatter.
     */
    public function trans(string $message, ?array $parameters = [], ?string $textDomain = self::TEXT_DOMAIN_DEFAULT, ?string $locale = null): string
    {
        $locale     = ($locale ?: $this->getLocale());
        $parameters = ($parameters ?: []);

        $message = parent::translate($message, $textDomain, $locale);

        if (\is_array($message))
        {
            \Tracy\Debugger::log($message);
        }

        return $this->mf($message, $parameters, $locale);
    }

    /**
     * Only format a message with MessageFormatter.
     */
    public function mf(string $message, ?array $parameters = [], ?string $locale = null): string
    {
        global $session;

        //-- Not do nothing if message is empty
        //-- MessageFormatter fail if message is empty
        if ('' == $message)
        {
            return '';
        }

        //-- Added same default values
        if ($session['user']['loggedin'] ?? false)
        {
            $parameters = \array_merge([
                'playerName' => $session['user']['name'] ?? '',
                'playerSex'  => $session['user']['sex'] ?? '',
                'location'   => $session['user']['location'] ?? '',
            ], $parameters);
        }

        $locale     = ($locale ?: $this->getLocale());
        $parameters = ($parameters ?: []);
        //-- Delete all values that not are allowed (can cause a error when use \MessageFormatter::format($params))
        $parameters = \array_filter($parameters, [$this, 'cleanParameters']);

        $formatter = new \MessageFormatter($locale, $message);

        $msg = $formatter->format($parameters);

        //-- Dump error to debug
        if ($formatter->getErrorCode())
        {
            bdump($formatter->getPattern());
            bdump($formatter->getErrorMessage());
        }

        return $msg;
    }

    /**
     * Clean param of a value.
     *
     * @param mixed $param
     */
    protected function cleanParameters($param): bool
    {
        if (
            \is_string($param) //-- Allow string values
            || \is_numeric($param) //-- Allow numeric values
            || \is_bool($param) //-- Allow bool values
            || \is_null($param) //-- Allow null value (Formatter can handle this value)
            || $param instanceof \DateTime //-- Allow DateTime object
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadMessages($textDomain, $locale)
    {
        if ( ! isset($this->messages[$textDomain]))
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

        if ( ! $messagesLoaded)
        {
            $discoveredTextDomain = null;

            if ($this->isEventManagerEnabled())
            {
                $until = function ($r)
                {
                    return $r instanceof TextDomain;
                };

                $event = new Event(self::EVENT_NO_MESSAGES_LOADED, $this, [
                    'locale'      => $locale,
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
            $messagesLoaded                       = true;
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
     * @throws Exception\RuntimeException When specified loader is not a file loader
     *
     * @return bool
     */
    protected function loadLotgdMessagesFromPatterns($namespace, $locale)
    {
        $messagesLoaded = false;

        //-- Use - as delimiter for folders.
        //-- Base structure is scope-domain
        $domains = \explode('-', $namespace);

        //-- Base scope is page
        if (\count($domains) < 2)
        {
            $domains[1] = $domains[0];
            $domains[0] = 'page';
        }

        $filename = \sprintf('translations/%s/%s.yaml', $locale, \implode('/', $domains));

        if (\is_file($filename))
        {
            $loader = $this->getPluginManager()->get('Yaml');

            if ( ! $loader instanceof FileLoaderInterface)
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
