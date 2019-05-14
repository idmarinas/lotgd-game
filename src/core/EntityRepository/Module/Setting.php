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

namespace Lotgd\Core\EntityRepository\Module;

use Tracy\Debugger;

/**
 * Functions for settings of modules.
 */
trait Setting
{
    /**
     * Find all modules that have
     *
     * @param string $like
     *
     * @return array
     */
    public function findModulesEditorNav(string $like): array
    {
        $query = $this->createQueryBuilder('u');
        // $sql = 'SELECT formalname,modulename,active,category FROM '.DB::prefix('modules')." WHERE infokeys LIKE '%|$like|%' ORDER BY category,formalname";

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
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }
}



