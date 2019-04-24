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

namespace Lotgd\Core\EntityRepository\Character;

use Tracy\Debugger;

/**
 * Functions for bio of characters.
 */
trait Bio
{
    /**
     * Get all bios of characters that NOT are blocked.
     *
     * @return array
     */
    public function getCharactersUnblockedBio(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.name', 'u.id', 'u.bio', 'u.biotime')
                ->where("u.biotime < '9999-12-31' and u.bio != ''")
                ->orderBy('u.biotime', 'DESC')
                ->setMaxResults(100)
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
     * Get all bios of characters that are blocked.
     *
     * @return array
     */
    public function getCharactersBlockedBio(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.name', 'u.id', 'u.bio', 'u.biotime')
                ->where("u.biotime > '9000-01-01' and u.bio != ''")
                ->orderBy('u.biotime', 'DESC')
                ->setMaxResults(100)
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
     * Block a bio of user.
     *
     * @param int $char
     *
     * @return bool
     */
    public function blockCharacterBio(int $char): bool
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->update($this->_entityName, 'u')
                ->set('u.bio', '?1')
                ->set('u.biotime', '?2')
                ->where('u.id = :char')
                ->setParameter('char', $char)
                ->setParameter(1, '`iBlocked for inappropriate usage´i')
                ->setParameter(2, new \DateTime('9999-12-31 23:59:59'))
                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Unblock a bio of user.
     *
     * @param int $char
     *
     * @return bool
     */
    public function unblockCharacterBio(int $char): bool
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->update($this->_entityName, 'u')
                ->set('u.bio', '?1')
                ->set('u.biotime', '?2')
                ->where('u.id = :char')
                ->setParameter('char', $char)
                ->setParameter(1, '')
                ->setParameter(2, new \DateTime('0000-00-00 00:00:00'))
                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }
}
