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
use Twig\Environment as ThemeCore;

trait Template
{
    protected $lotgdTemplateParams;
    protected $lotgdTemplateSystem;

    /**
     * Get template params instance.
     *
     * @return object|null
     */
    public function getTemplateParams()
    {
        if ( ! $this->lotgdTemplateParams instanceof Params)
        {
            $this->lotgdTemplateParams = $this->getService(Params::class);
        }

        return $this->lotgdTemplateParams;
    }

    /**
     * Get theme instance.
     *
     * @return object|null
     */
    public function getTemplate()
    {
        if ( ! $this->lotgdTemplateSystem instanceof ThemeCore)
        {
            $this->lotgdTemplateSystem = $this->getService('twig');
        }

        return $this->lotgdTemplateSystem;
    }
}
