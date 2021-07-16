<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat\Battle;

trait Other
{
    public function getAutoAttackCount(): int
    {
        $count = 1;
        $auto = $this->request->query->getAlnum('auto');

        if ('full' == $auto)
        {
            //-- Limit count to a max of 50. To avoid saturate the server
            $count = 50;
        }
        elseif ('five' == $auto)
        {
            $count = 5;
        }
        elseif ('ten' == $auto)
        {
            $count = 10;
        }

        return $count;
    }
}
