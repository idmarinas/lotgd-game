<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Tool;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Bundle\CoreBundle\Entity\Titles;

class Lotgd
{
    private $doctrine;

    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Get the appropriate title for the character based on their dragonkill counter.
     */
    public function getCharacterTitle(int $dragonkills = 0): ?Titles
    {
        $query  = $this->doctrine->createQueryBuilder();
        $result = $query
            ->select('u')
            ->from('LotgdCore:Titles', 'u')
            ->where('u.dk <= :dk')
            ->orderBy('rand()')

            ->setParameter('dk', $dragonkills)

            ->setMaxResults(1)

            ->getQuery()
            ->getSingleResult()
        ;

        return $result ?: null;
    }
}
