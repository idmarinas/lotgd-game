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
    const TEXT_DOMAIN_DEFAULT = 'page-default';

    /**
     * Translate a message of LoTGD WITH MessageFormatter.
     *
     * @param string      $message
     * @param array|null  $parameters
     * @param string|null $textDomain
     * @param string|null $locale
     *
     * @return string
     */
    public function trans(string $message, ?array $parameters = [], ?string $textDomain = self::TEXT_DOMAIN_DEFAULT, ?string $locale = null): string
    {
        $locale = ($locale ?: $this->getLocale());
        $parameters = ($parameters ?: []);

        $message = $this->st($message, $textDomain, $locale);

        if (is_array($message))
        {
            //-- Can change union of array with this
            $union = $message['union'] ?? ' ';
            unset($message['union']);

            return $this->mf(\implode($union, $message), $parameters, $locale);
        }

        return $this->mf($message, $parameters, $locale);
    }

    /**
     * Only format a message with MessageFormatter.
     *
     * @param string      $message
     * @param array|null  $parameters
     * @param string|null $locale
     *
     * @return string
     */
    public function mf(string $message, ?array $parameters = [], ?string $locale = null): string
    {
        //-- Not do nothing if message is empty
        //-- MessageFormatter fail if message is empty
        if ('' == $message)
        {
            return '';
        }

        $locale = ($locale ?: $this->getLocale());
        $parameters = ($parameters ?: []);
        //-- Delete all values that not are allowed (can cause a error when use \MessageFormatter::format($params))
        $parameters = array_filter($parameters, [$this, 'cleanParameters']);

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
     * Only select a translation WITHOUT MessageFormatter.
     *
     * @param string      $message
     * @param string|null $textDomain
     * @param string|null $locale
     *
     * @return array|string
     */
    public function st(string $message, ?string $textDomain = self::TEXT_DOMAIN_DEFAULT, ?string $locale = null)
    {
        $locale = ($locale ?: $this->getLocale());

        return parent::translate($message, $textDomain ?? self::TEXT_DOMAIN_DEFAULT, $locale);
    }

    /**
     * Clean param of .
     *
     * @param mixed $param
     *
     * @return bool
     */
    protected function cleanParameters($param): bool
    {
        $return = false;

        if (
            \is_string($param) //-- Allow string values
            || \is_numeric($param) //-- Allow numeric values
            || \is_bool($param) //-- Allow bool values
            || \is_null($param) //-- Allow null value (Formatter can handle this value)
            || $param instanceof \DateTime //-- Allow DateTime object
        ) {
            $return = true;
        }

        return $return;
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

        //-- Use - as delimiter for folders.
        //-- Base structure is scope-domain
        $domains = \explode('-', $namespace);

        //-- Base scope is page
        if (count($domains) < 2)
        {
            $domains[1] = $domains[0];
            $domains[0] = 'page';
        }

        $filename = sprintf('data/translation/%s/%s.yaml', $locale, \implode('/', $domains));

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
