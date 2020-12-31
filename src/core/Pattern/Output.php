<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Pattern;

use Lotgd\Core\Output\Code;
use Lotgd\Core\Output\Color;

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
            $this->lotgdColor = $this->getContainer(Color::class);
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
            $this->lotgdCode = $this->getContainer(Code::class);
        }

        return $this->lotgdCode;
    }
}
