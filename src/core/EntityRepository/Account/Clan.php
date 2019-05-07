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

namespace Lotgd\Core\EntityRepository\Account;

use Doctrine\ORM\Query\Expr\Join;
use Lotgd\Core\Entity as EntityCore;
use Tracy\Debugger;

/**
 * Functions for clans.
 */
trait Clan
{
    /**
     * Get author name for description and motd.
     *
     * @param int $motdAuthor
     * @param int $descAuthor
     *
     * @return array|null
     */
    public function getClanAuthorNameOfMotdDescFromAcctId(int $motdAuthor, int $descAuthor): ?array
    {
        $query = $this->createQueryBuilder('u');
        $descQuery = $this->createQueryBuilder('s');

        try
        {
            $descQuery->select('ch.name')
                ->leftJoin(EntityCore\Characters::class, 'ch', Join::WITH, $descQuery->expr()->eq('ch.id', 's.character'))
                ->where('s.acctid = :acctDesc')
            ;

            return $query->select('cm.name AS motdauthname', '('.$descQuery->getDQL().') AS descauthname')
                ->leftJoin(EntityCore\Characters::class, 'cm', Join::WITH, $query->expr()->eq('cm.id', 'u.character'))
                ->where('u.acctid = :acct')
                ->setParameter('acct', $motdAuthor)
                ->setParameter('acctDesc', $descAuthor)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }
}
