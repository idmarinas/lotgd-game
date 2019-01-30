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

namespace Lotgd\Core\Translator\Loader;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Zend\I18n\Exception;
use Zend\I18n\Translator\Loader\AbstractFileLoader;
use Zend\I18n\Translator\Loader\FileLoaderInterface;
use Zend\I18n\Translator\TextDomain;

/**
 * Load a Yaml file.
 */
class Yaml extends AbstractFileLoader implements FileLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($locale, $filename)
    {
        $resolvedIncludePath = stream_resolve_include_path($filename);
        $fromIncludePath = (false !== $resolvedIncludePath) ? $resolvedIncludePath : $filename;

        if (! $fromIncludePath || ! is_file($fromIncludePath) || ! is_readable($fromIncludePath))
        {
            throw new Exception\InvalidArgumentException(sprintf(
                'Could not find or open file %s for reading',
                $filename
            ));
        }

        $yamlParser = new YamlParser();

        try
        {
            $messages = $yamlParser->parseFile($fromIncludePath, SymfonyYaml::PARSE_CONSTANT);
        }
        catch (ParseException $e)
        {
            throw new Exception\InvalidArgumentException(sprintf('Error parsing YAML, invalid file "%s"', $fromIncludePath), 0, $e);
        }

        $messages = $this->flatten($messages);

        if (! is_array($messages))
        {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an array, but received %s',
                gettype($messages)
            ));
        }

        $textDomain = new TextDomain($messages);

        if (array_key_exists('', $textDomain))
        {
            if (isset($textDomain['']['plural_forms']))
            {
                $textDomain->setPluralRule(
                    PluralRule::fromString($textDomain['']['plural_forms'])
                );
            }

            unset($textDomain['']);
        }

        return $textDomain;
    }

    /**
     * Flattens an nested yaml of translations.
     *
     * The scheme used is:
     *   'key':
     *      'key2':
     *          'key3': 'value'
     *
     * Becomes:
     *   'key.key2.key3' => 'value'
     *
     * @param array  $messages
     * @param array  $node     Internal use
     * @param string $path     Internal use
     */
    private function flatten(array $messages, array $node = null, $path = null)
    {
        if (null === $node)
        {
            $node = $messages;
        }

        foreach ($node as $key => $value)
        {
            if (\is_array($value))
            {
                $nodePath = $path ? $path.'.'.$key : $key;
                $messages = $this->flatten($messages, $value, $nodePath);

                if (null === $path)
                {
                    unset($messages[$key]);
                }
            }
            elseif (null !== $path)
            {
                $messages[$path.'.'.$key] = $value;
            }
        }

        return $messages;
    }
}
