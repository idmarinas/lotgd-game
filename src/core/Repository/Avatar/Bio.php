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

namespace Lotgd\Core\Repository\Avatar;

use Throwable;
use DateTime;
use Tracy\Debugger;

/**
 * Functions for bio of characters.
 */
trait Bio
{
    /**
     * Get all bios of characters that NOT are blocked.
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
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Get all bios of characters that are blocked.
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
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Block a bio of user.
     */
    public function blockCharacterBio(int $char): bool
    {
        return $this->blockUnblockCharacterBio($char, '`iBlocked for inappropriate usage´i', new DateTime('9999-12-31 23:59:59'));
    }

    /**
     * Unblock a bio of user.
     */
    public function unblockCharacterBio(int $char): bool
    {
        return $this->blockUnblockCharacterBio($char, '', new DateTime('0000-00-00 00:00:00'));
    }

    private function blockUnblockCharacterBio(int $char, string $block, $date): bool
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->update($this->_entityName, 'u')
                ->set('u.bio', '?1')
                ->set('u.biotime', '?2')
                ->where('u.id = :char')
                ->setParameter('char', $char)
                ->setParameter(1, $block)
                ->setParameter(2, $date)
                ->getQuery()
                ->execute()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }
}
