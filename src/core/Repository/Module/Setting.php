<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Repository\Module;

use Throwable;
use Tracy\Debugger;

/**
 * Functions for settings of modules.
 */
trait Setting
{
    /**
     * Find all modules that have.
     */
    public function findModulesEditorNav(string $like): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query
                ->where('u.infokeys LIKE ?1')
                ->setParameter(1, "%|{$like}|%")
                ->orderBy('u.category', 'ASC')
                ->addOrderBy('u.formalname', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }
}
