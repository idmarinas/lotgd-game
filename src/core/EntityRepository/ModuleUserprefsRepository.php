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

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Doctrine\ORM\EntityRepository as DoctrineRepository;
use Tracy\Debugger;

class ModuleUserprefsRepository extends DoctrineRepository
{
    /**
     * Find modules prefs for modules.
     *
     * @param array $modules
     *
     * @return array
     */
    public function findModulesPrefs(array $modules, int $acctId): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query
                ->where('u.modulename IN (:modules) AND u.userid = :acct')
                ->andWhere("u.setting LIKE 'user_%' OR u.setting LIKE 'check_%'")

                ->setParameter('modules', $modules)
                ->setParameter('acct', $acctId)

                ->orderBy('u.modulename', 'DESC')

                ->getQuery()
                ->getArrayResult()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }
}
