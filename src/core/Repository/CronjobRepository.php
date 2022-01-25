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

namespace Lotgd\Core\Repository;

use Lotgd\Core\Repository\Avatar\Bio;
use Lotgd\Core\Repository\Avatar\Clan;
use Lotgd\Core\Repository\Avatar\Setting;
use Throwable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Cronjob;
use Tracy\Debugger;

class CronjobRepository extends ServiceEntityRepository
{
    use Bio;
    use Clan;
    use Setting;
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cronjob::class);
    }

    /**
     * Get character's name from an account ID.
     *
     * @return string
     */
    public function getCharacterNameFromAcctId(int $acctId): ?string
    {
        $qb = $this->createQueryBuilder('u');

        try
        {
            return $qb->select('u.name')
                ->where('u.acct = :acct')
                ->setParameters(['acct' => $acctId])
                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }
}
