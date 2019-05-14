<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\EntityRepository\Character;

use Tracy\Debugger;

/**
 * Functions for settings of characters.
 */
trait Setting
{
    /**
     * Moving players to new location.
     *
     * @param string $from
     * @param string $to
     *
     * @return bool
     */
    public function movingPlayersToLocation(string $from, string $to): bool
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->update($this->_entityName, 'u')
                ->set('u.location', '?1')
                ->where('u.location = ?2')
                ->setParameter(1, $to)
                ->setParameter(2, $from)
                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }
}
