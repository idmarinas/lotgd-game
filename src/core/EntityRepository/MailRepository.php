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

class MailRepository extends DoctrineRepository
{
    use Mail\Backup;
    use Mail\Clan;

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
            return $default;
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
            Debugger::log($th);

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
            Debugger::log($th);

            return null;
        }
    }

    /**
     * Get list of messages for a character.
     *
     * @param int    $acctId
     * @param string $order
     * @param int    $direction
     *
     * @return array
     */
    public function getCharacterMail(int $acctId, string $order, int $direction): array
    {
        $query = $this->createQueryBuilder('u');
        $expr = $query->expr();

        switch ($order)
        {
            case 'subject':
                $order = 'u.subject';
            break;
            case 'name':
                $order = 'c.name';
            break;
            default: //date
                $order = 'u.sent';
            break;
        }

        $direction = $direction ? 'ASC' : 'DESC';

        try
        {
            return $query->select('u.messageid', 'u.subject', 'u.msgfrom', 'u.seen', 'u.sent')
                ->addSelect('c.name')
                ->addSelect('a.loggedin')
                ->leftJoin('LotgdCore:Characters', 'c', 'WITH', $expr->eq('c.acct', 'u.msgfrom'))
                ->leftJoin('LotgdCore:Accounts', 'a', 'WITH', $expr->eq('a.acctid', 'u.msgfrom'))

                ->orderBy($order, $direction)

                ->where('u.msgto = :acct')

                ->setParameter('acct', $acctId)

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

    /**
     * Get a names of senders and count msgs.
     *
     * @param int $acctId
     *
     * @return array
     */
    public function getMailSenderNames(int $acctId): array
    {
        $query = $this->createQueryBuilder('u');
        $expr = $query->expr();

        try
        {
            return $query->select('u.msgfrom', 'count(u.msgfrom) as count')
                ->addSelect('c.name')
                ->leftJoin('LotgdCore:Characters', 'c', 'WITH', $expr->eq('c.acct', 'u.msgfrom'))

                ->where('u.msgto = :acct')

                ->groupBy('u.msgfrom')

                ->setParameter('acct', $acctId)

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

    /**
     * Get next and previous IDs of current mail.
     *
     * @param int $mailId
     * @param int $acctId
     *
     * @return array
     */
    public function getNextPreviousMail(int $mailId, int $acctId): array
    {
        $query = $this->createQueryBuilder('u');
        $next = $this->createQueryBuilder('n');
        $prev = $this->createQueryBuilder('p');

        try
        {
            $next->select('min(n.messageid)')
                ->where('n.messageid > :mail AND n.msgto = :acct')
                ->orderBy('n.messageid', 'ASC')

                ->setMaxResults(1)
            ;

            $prev->select('max(p.messageid)')
                ->where('p.messageid < :mail AND p.msgto = :acct')
                ->orderBy('p.messageid', 'DESC')

                ->setMaxResults(1)
            ;

            return $query->select('u.messageid')
                ->addSelect('('.$prev->getDQL().' ) AS previous')
                ->addSelect('('.$next->getDQL().' ) AS next')

                ->setParameter('mail', $mailId)
                ->setParameter('acct', $acctId)

                ->setMaxResults(1)

                ->getQuery()
                ->getSingleResult()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Delete mail in bulk by ID.
     *
     * @param array $ids
     *
     * @return int
     */
    public function deleteBulkMail(array $ids)
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->delete($this->_entityName, 'u')
                ->where('u.messageid IN (:ids)')
                ->setParameter('ids', $ids)
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
     * Get info of the reply message.
     *
     * @param int $msgId
     * @param int $acct
     *
     * @return array
     */
    public function replyToMessage(int $msgId, int $acct): array
    {
        $query = $this->createQueryBuilder('u');
        $expr = $query->expr();

        try
        {
            return $query->select('u.sent', 'u.body', 'u.msgfrom', 'u.subject')
                ->addSelect('a.acctid', 'a.superuser')
                ->addSelect('c.name')

                ->leftJoin('LotgdCore:Accounts', 'a', 'WITH', $expr->eq('a.acctid', 'u.msgfrom'))
                ->leftJoin('LotgdCore:Characters', 'c', 'WITH', $expr->eq('c.acct', 'u.msgfrom'))

                //-- Can not reply to system messages
                ->where('u.msgto = :acct AND u.messageid = :id AND u.msgfrom != 0')

                ->setParameter('id', $msgId)
                ->setParameter('acct', $acct)

                ->getQuery()
                ->getSingleResult()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Count messages in inbox of character.
     *
     * @param int $acctId
     * @param int $onlyUnseen
     *
     * @return int
     */
    public function countInboxOfCharacter(int $acctId, int $onlyUnseen): int
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            if ($onlyUnseen)
            {
                $query->where('u.seen = 0');
            }

            return $query->select('count(u.messageid)')

                //-- Can not reply to system messages
                ->andWhere('u.msgto = :acct')

                ->setParameter('acct', $acctId)

                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }

    /**
     * Delte old mails in data base.
     *
     * @param int $expire
     *
     * @return int
     */
    public function deleteExpireMail(int $expire): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            $date = new \DateTime('now');
            $date->sub(new \DateInterval("P{$expire}D"));

            return $query->delete($this->_entityName, 'u')
                ->where('u.sent < :date')
                ->setParameter('date', $date)
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
}
