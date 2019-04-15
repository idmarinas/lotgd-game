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
use Lotgd\Core\ServiceManager;
use Lotgd\Core\Translator\Translator as CoreTranslator;
use Lotgd\Core\Twig\NodeVisitor\TranslatorDefaultDomainNodeVisitor;
use Lotgd\Core\Twig\NodeVisitor\TranslatorNodeVisitor;
use Lotgd\Core\Twig\TokenParser\TranslatorDefaultDomainTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Translator extends AbstractExtension
{
    use PatternCore\Container;
    use PatternCore\Translator;
    use Pattern\Translator;

    protected $translator;
    protected $translatorNodeVisitor;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->setContainer($serviceManager);
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
            new TwigFilter('t', [$this, 'translate']),
            /*
             * Use "tl" when you want to change domain to translate a text.
             * Only need if you use "translate_default_domain" in template.
             */
            new TwigFilter('tl', [$this, 'translate']),
            /*
             * Use MessageFormatter to formater message.
             */
            new TwigFilter('tmf', [$this, 'translateMf']),
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
