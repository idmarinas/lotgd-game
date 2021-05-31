<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Pattern;

use Lotgd\Core\Output\Code;
use Lotgd\Core\Output\Color;

@trigger_error(Output::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
trait Output
{
    protected $lotgdOutput;
    protected $lotgdColor;
    protected $lotgdCode;

    /**
     * Get color instance.
     */
    public function getColor(): Color
    {
        if ( ! $this->lotgdColor instanceof Color)
        {
            $this->lotgdColor = $this->getService(Color::class);
        }

        return $this->lotgdColor;
    }

    /**
     * Get Instance of code.
     */
    public function getCode(): Code
    {
        if ( ! $this->lotgdCode instanceof Code)
        {
            $this->lotgdCode = $this->getService(Code::class);
        }

        return $this->lotgdCode;
    }
}
