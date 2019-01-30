<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension;

use Lotgd\Core\Translator\Translator as CoreTranslator;
use Lotgd\Core\Twig\NodeVisitor\TranslatorDefaultDomainNodeVisitor;
use Lotgd\Core\Twig\NodeVisitor\TranslatorNodeVisitor;
use Lotgd\Core\Twig\TokenParser\TranslatorDefaultDomainTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Translator extends AbstractExtension
{
    protected $translator;
    protected $translatorNodeVisitor;

    /**
     * @param CoreTranslator $translator
     */
    public function __construct(CoreTranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return [$this->getTranslationNodeVisitor(), new TranslatorDefaultDomainNodeVisitor()];
    }

    public function getTranslationNodeVisitor()
    {
        return $this->translatorNodeVisitor ?: $this->translatorNodeVisitor = new TranslatorNodeVisitor();
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            /**
             * {% translate_default_domain 'foobar' %}
             */
            new TranslatorDefaultDomainTokenParser(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('t', [$this, 'translate']),
        ];
    }

    /**
     * Translate a string using translator.
     *
     * @param string $message
     * @param array  $arguments
     * @param string $domain
     * @param string $locale
     *
     * @return string
     */
    public function translate($message, array $parameters = [], $domain = null, $locale = null): string
    {
        return $this->translator->trans($message, $parameters, $domain, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'translator';
    }
}
