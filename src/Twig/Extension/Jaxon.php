<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.0.0
 */

namespace Lotgd\Core\Twig\Extension;

use Jaxon\AjaxBundle\Jaxon as CoreJaxon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Jaxon extends AbstractExtension
{
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
            new TwigFunction('jaxon_css', [$this->jaxon, 'getCss'], ['is_safe' => ['html']]),
            new TwigFunction('jaxon_js', [$this->jaxon, 'getJs'], ['is_safe' => ['html']]),
            new TwigFunction('jaxon_script', [$this->jaxon, 'getScript'], ['is_safe' => ['html']]),
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
