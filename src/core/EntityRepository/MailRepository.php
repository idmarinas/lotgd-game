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

use Doctrine\ORM\EntityRepository as DoctrineRepository;

class MailRepository extends DoctrineRepository
{
    /**
     * Get a count of see and unsee mail.
     *
     * @param int $acctId
     *
     * @return array
     */
    public function getCountMailOfCharacter(int $acctId): array
    {
        $default = [
            'seenCount' => 0,
            'notSeenCount' => 0
        ];

        if (! $acctId)
        {
            return [
                'seenCount' => 0,
                'notSeenCount' => 0
            ];
        }

        $qb = $this->createQueryBuilder('u');

        try
        {
            return $qb->select(
                    'SUM(CASE WHEN u.seen = 1 THEN 1 ELSE 0 END) AS seenCount',
                    'SUM(CASE WHEN u.seen = 0 THEN 1 ELSE 0 END) AS notSeenCount'
                )
                ->where('u.msgto = :acct')
                ->setParameters(['acct' => $acctId])
                ->getQuery()
                ->getSingleResult()
            ;
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            return $default;
        }
    }

    /**
     * Get character's name from an account ID.
     *
     * @param int $acctId
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
        catch (\Throwable $th)
        {
            return null;
        }
    }
}
