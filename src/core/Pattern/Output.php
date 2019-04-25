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

use Lotgd\Core\Output\Collector as OutputCore;

trait Output
{
    protected $lotgdOutput;

    /**
     * Get output instance.
     *
     * @return object|null
     */
    public function getOutput()
    {
        if (! $this->lotgdOutput instanceof OutputCore)
        {
            $this->lotgdOutput = $this->getContainer(OutputCore::class);
        }

        return $this->lotgdOutput;
    }
}
