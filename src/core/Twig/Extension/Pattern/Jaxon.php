<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.5.0
 */

namespace Lotgd\Core\Twig\Extension\Pattern;

/**
 * Trait to output Jaxon files.
 */
trait Jaxon
{
    /**
     * Get Jaxon CSS.
     */
    public function jaxonCss(): string
    {
        return $this->getJaxon()->getCss();

        // $html['scripthead'] .= $lotgdJaxon->getJs();
        // $html['scripthead'] .= $lotgdJaxon->getScript();
    }

    /**
     * Get Jaxon Js.
     *
     * @return string
     */
    public function jaxonJs(): string
    {
        return $this->getJaxon()->getJs();
    }

    /**
     * Get Jaxon Script.
     *
     * @return string
     */
    public function jaxonScript(): string
    {
        return $this->getJaxon()->getScript();
    }
}
