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

namespace Lotgd\Bundle\SettingsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Bundle\SettingsBundle\Entity\SettingDomain;

/**
 * @method SettingDomain|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingDomain|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingDomain[]    findAll()
 * @method SettingDomain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingDomainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettingDomain::class);
    }
}
