<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Repository;

use Lotgd\Bundle\CoreBundle\Entity\Petition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Petition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Petition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Petition[]    findAll()
 * @method Petition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Petition::class);
    }

    /**
     * Get count of petitions for network.
     */
    public function getCountPetitionsForNetwork(string $ip): int
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $date = new \DateTime('now');
            $date->sub(new \DateInterval('P1D'));

            return $query->select('count(1)')
                ->where('inet_aton(u.ip) LIKE inet_aton(:ip) AND u.status != "unhandled"')
                ->andWhere('u.createdAt > :date')
                ->setParameter('date', $date)
                ->setParameter('ip', $ip)

                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (\Throwable $th)
        {
            return 0;
        }
    }
}
