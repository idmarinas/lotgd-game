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
     *      'key4':
     *          - 'value1'
     *          - 'value2'
     *      'key5':
     *          0: 'value3'
     *          1: 'value4'
     *      'key6':
     *          '0': 'value5'
     *          '1': 'value6'
     *      'key7':
     *          '00': 'value7'
     *          '01': 'value8'
     *
     * Becomes:
     * [
     *    'key.key2.key3' => 'value',
     *    'key.key4' => [
     *       0 => 'value1',
     *       1 => 'value2'
     *    ],
     *    'key.key5' => [
     *       0 => 'value3',
     *       1 => 'value4'
     *    ],
     *    'key.key6' => [
     *       0 => 'value5',
     *       1 => 'value6'
     *    ],
     *    'key.key7.00' => 'value7',
     *    'key.key7.01' => 'value8',
     * ]
     *
     * @TODO In PHP 7.3.0 Use array_key_first() to avoid use of reset() and key()
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
            \is_array($value) ? reset($value) : null;
            if (\is_array($value) && ! is_int(\key($value)))
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
