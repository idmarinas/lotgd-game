<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.0.0
 */

namespace Lotgd\Core\Twig\Extension;

use Lotgd\Core\Service\Jaxon as CoreJaxon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Jaxon extends AbstractExtension
{
    use Pattern\Jaxon;

    protected $jaxon;

    public function __construct(CoreJaxon $jaxon)
    {
        $this->jaxon = $jaxon;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('jaxon_css', [$this, 'jaxonCss']),
            new TwigFunction('jaxon_js', [$this, 'jaxonJs']),
            new TwigFunction('jaxon_script', [$this, 'jaxonScript']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jaxon';
    }
}
