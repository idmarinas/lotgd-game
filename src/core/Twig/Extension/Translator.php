<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Translator extends AbstractExtension
{
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            //-- Use MessageFormatter to formater message.
            new TwigFilter('tmf', [$this, 'messageFormatter']), //-- Alias
            new TwigFilter('mf', [$this, 'messageFormatter']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('translator_locale', [$this, 'translatorDefaultLocale']),
        ];
    }

    /**
     * Only format a message with MessageFormatter.
     */
    public function messageFormatter(?string $message, ?array $parameters = [], ?string $locale = null): string
    {
        //-- Not do nothing if message is empty
        //-- MessageFormatter fail if message is empty
        if ( ! $message)
        {
            return '';
        }

        $locale     = ($locale ?: $this->translator->getLocale());
        $parameters = ($parameters ?: []);
        //-- Delete all values that not are allowed (can cause a error when use \MessageFormatter::format($params))
        $parameters = \array_filter($parameters, [$this, 'cleanParameters']);

        $formatter = new \MessageFormatter($locale, $message);

        $msg = $formatter->format($parameters);

        //-- Dump error to debug
        if ($formatter->getErrorCode() !== 0)
        {
            bdump($formatter->getPattern());
            bdump($formatter->getErrorMessage());
        }

        return $msg;
    }

    /**
     * Get locale for translator.
     */
    public function translatorDefaultLocale(): string
    {
        return $this->translator->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'translator';
    }

    /**
     * Clean param of a value.
     *
     * @param mixed $param
     */
    private function cleanParameters($param): bool
    {
        return \is_string($param) //-- Allow string values
        || \is_numeric($param) //-- Allow numeric values
        || \is_bool($param) //-- Allow bool values
        || \is_null($param) //-- Allow null value (Formatter can handle this value)
        || $param instanceof \DateTime; //-- Allow DateTime object
    }
}
