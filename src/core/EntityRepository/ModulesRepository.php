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

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Doctrine\ORM\EntityRepository as DoctrineRepository;
use Tracy\Debugger;

class ModulesRepository extends DoctrineRepository
{
    use Module\Setting;

    /**
     * Restart filemoddate to default value.
     */
    public function reinstallModule(string $module): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->update($this->_entityName, 'u')
                ->set('u.filemoddate', ':date')
                ->where('u.modulename = :module')

                ->setParameter('module', $module)
                ->setParameter('date', (new \DateTime('0000-00-00 00:00:00'))->format(\DateTime::ISO8601))

                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }

    /**
     * Find modules with this info key.
     */
    public function findInfoKeyLike(string $key): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.modulename')
                ->where('u.infokeys LIKE :key AND u.active = 1')

                ->setParameter('key', "%|{$key}|%")

                ->orderBy('u.modulename', 'DESC')

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
