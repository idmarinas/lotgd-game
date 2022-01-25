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

namespace Lotgd\Core\Repository\User;

use Throwable;
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
     */
    public function getClanAuthorNameOfMotdDescFromAcctId(int $motdAuthor, int $descAuthor): ?array
    {
        $query     = $this->createQueryBuilder('u');
        $descQuery = $this->createQueryBuilder('s');

        try
        {
            $descQuery->select('ch.name')
                ->leftJoin('LotgdCore:Avatar', 'ch', Join::WITH, $descQuery->expr()->eq('ch.id', 's.avatar'))
                ->where('s.acctid = :acctDesc')
            ;

            return $query->select('cm.name AS motdauthname', '('.$descQuery->getDQL().') AS descauthname')
                ->leftJoin('LotgdCore:Avatar', 'cm', Join::WITH, $query->expr()->eq('cm.id', 'u.avatar'))
                ->where('u.acctid = :acct')
                ->setParameter('acct', $motdAuthor)
                ->setParameter('acctDesc', $descAuthor)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }
}
