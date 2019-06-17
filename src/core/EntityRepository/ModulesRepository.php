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

class ModulesRepository extends DoctrineRepository
{
    use Module\Setting;

    /**
     * Restart filemoddate to default value.
     *
     * @param string $module
     *
     * @return int
     */
    public function reinstallModule(string $module): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->update($this->_entityName, 'u')
                ->set('u.filemoddate', '0000-00-00 00:00:00')

                ->where('u.modulename = :module')
                ->setParameter('module', $module)

                ->getQuery()
                ->execute();
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }
}
