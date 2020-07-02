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

use Lotgd\Core\Pattern as PatternCore;
use Lotgd\Core\Twig\NodeVisitor\{
    TranslatorDefaultDomainNodeVisitor,
    TranslatorNodeVisitor
};
use Lotgd\Core\Twig\TokenParser\TranslatorDefaultDomainTokenParser;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Translator extends AbstractExtension
{
    use PatternCore\Container;
    use PatternCore\Translator;
    use Pattern\Translator;

    protected $translator;
    protected $translatorNodeVisitor;

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
             * @param string
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
            new TwigFilter('t', [$this, 'translate'], ['needs_environment' => true]),
            new TwigFilter('trans', [$this, 'translate'], ['needs_environment' => true]),
            /**
             * Use "tl" when you want to change domain to translate a text.
             * Only need if you use "translate_default_domain" in template.
             */
            new TwigFilter('tl', [$this, 'translate'], ['needs_environment' => true]),
            /**
             * Use MessageFormatter to formater message.
             */
            new TwigFilter('tmf', [$this, 'translateMf'], ['needs_environment' => true]),
            /**
             * Only select a translation WITHOUT MessageFormatter.
             */
            new TwigFilter('tst', [$this, 'translateSt'], ['needs_environment' => true]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('translator_locale', [$this, 'translatorDefaultLocale'])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'translator';
    }
}
