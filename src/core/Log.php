<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.3.0
 */

namespace Lotgd\Core;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Entity\Debuglog;
use Lotgd\Core\Entity\Gamelog;

class Log
{
    private $doctrine;

    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Log a message from game.
     *
     * @param string $message  Message of log
     * @param string $category Category of log
     * @param bool   $filed    If log is filed
     */
    public function game(string $message, string $category = 'general', bool $filed = false): void
    {
        global $session;

        $entity = (new Gamelog())
            ->setMessage($message)
            ->setCategory($category)
            ->setFiled($filed)
            ->setDate(new DateTime('now'))
            ->setWho((int) $session['user']['acctid'])
        ;

        $this->doctrine->persist($entity);
        $this->doctrine->flush();
    }

    /**
     * Add to the user's log
     * if $field $value and $consolidate have values, entry will be merged with existing line from today with identical $field.
     * otherwise, a new line will be added to the log.
     *
     * @param string      $message     the text to be added
     * @param int|null    $target      acctid of the user on the receiving end of the event eg. the user who did NOT initiate PvP, gold transfer recipient (optional)
     * @param int|null    $user        acctid of the user the log entry is about (optional, defaults to current user)
     * @param string|null $field       the label for this line, appears as first word on this line in the log eg. healing, forestwin (optional)
     * @param int|null    $value       how much was gained or lost.  Only useful if also using $field and $consolidate (optional)
     * @param bool        $consolidate add $value to previous log lines with the same $field, keeping a running total for today (optional, defaults to true)
     */
    public function debug(string $message, ?int $target = null, ?int $user = null, ?string $field = null, ?int $value = null, bool $consolidate = true)
    {
        global $session;

        /** @var Lotgd\Core\Repository\DebuglogRepository $repository */
        $repository = $this->doctrine->getRepository('LotgdCore:Debuglog');

        $corevalue = $value;
        $target    = $target ?: 0;
        $field     = $field ?: '';
        $user      = $user ?: $session['user']['acctid'];
        $id        = 0;

        if ($field && null !== $value && $consolidate)
        {
            $query  = $repository->createQueryBuilder('u');
            $result = $query
                ->where('u.actor = :user AND u.field = :field AND u.date > :date')

                ->setParameter('user', $user)
                ->setParameter('field', $field)
                ->setParameter('date', new DateTime(date('Y-m-d 00:00:00')))

                ->getQuery()
                ->getResult()
            ;

            if ( ! empty($result))
            {
                $result  = $result[0];
                $value   = $result->getValue() + $value;
                $message = $result->getMessage();
                $id      = $result->getId();
            }
            unset($result);
        }

        if (false !== $corevalue)
        {
            $message .= " ({$corevalue})";
        }

        $value = $value ?: 0;

        /** @var Debuglog $entity */
        $entity = $repository->find($id) ?: new Debuglog();
        $entity->setDate(new DateTime('now'))
            ->setActor($user)
            ->setTarget($target)
            ->setMessage($message)
            ->setField($field)
            ->setValue($value)
        ;

        $this->doctrine->persist($entity);
        $this->doctrine->flush();
    }
}
