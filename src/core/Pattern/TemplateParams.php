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

namespace Lotgd\Core\Pattern;

use Lotgd\Core\Template\Params;

trait TemplateParams
{
    protected $lotgdTemplateParams;

    /**
     * Get template params instance.
     *
     * @return object|null
     */
    public function getTemplateParams()
    {
        if ( ! $this->lotgdTemplateParams instanceof Params)
        {
            $this->lotgdTemplateParams = $this->getContainer(Params::class);
        }

        return $this->lotgdTemplateParams;
    }
}
