<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
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
        return $this->jaxon->getCss();
    }

    /**
     * Get Jaxon Js.
     */
    public function jaxonJs(): string
    {
        return $this->jaxon->getJs();
    }

    /**
     * Get Jaxon Script.
     */
    public function jaxonScript(): string
    {
        return $this->jaxon->getScript();
    }
}
