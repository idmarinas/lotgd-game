<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.5.0
 */

namespace Lotgd\Core\Pattern;

use Lotgd\Core\Service\Jaxon as CoreJaxon;

trait Jaxon
{
    protected $lotgdJaxon;

    /**
     * Get Jaxon instance.
     *
     * @return object|null
     */
    public function getJaxon()
    {
        if ( ! $this->lotgdJaxon instanceof CoreJaxon)
        {
            $this->lotgdJaxon = $this->getService('lotgd.core.jaxon');
        }

        return $this->lotgdJaxon;
    }
}
