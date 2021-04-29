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

use Lotgd\Bundle\CoreBundle\Entity\PetitionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PetitionType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PetitionType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PetitionType[]    findAll()
 * @method PetitionType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetitionTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetitionType::class);
    }
}
