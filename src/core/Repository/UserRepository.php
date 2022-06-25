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

namespace Lotgd\Core\Repository;

use Lotgd\Core\Repository\User\Bans;
use Lotgd\Core\Repository\User\Avatar;
use Lotgd\Core\Repository\User\Clan;
use Lotgd\Core\Repository\User\Login;
use Lotgd\Core\Repository\User\Superuser;
use Throwable;
use DateTime;
use DateInterval;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Laminas\Hydrator\ClassMethodsHydrator;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity as LotgdEntity;
use Lotgd\Core\Entity\User as UserEntity;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tracy\Debugger;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    use EntityRepositoryTrait;
    use Bans;
    use Avatar;
    use Clan;
    use Login;
    use Superuser;
    use User\User;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEntity::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @return never
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if ( ! $user instanceof UserEntity)
        {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Get data of user by ID of account.
     */
    public function getUserById(int $acctId): ?array
    {
        $qb = $this->createQueryBuilder('u');

        try
        {
            $data = $qb->addSelect('ep')
                ->where('u.acctid = :acctid')
                ->setParameter('acctid', $acctId)
                ->leftJoin(LotgdEntity\AccountsEverypage::class, 'ep', Join::WITH, $qb->expr()->eq('ep.acctid', 'u.acctid'))
                ->getQuery()
                ->getResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return null;
        }

        //-- Fail if not found
        if (0 == \count($data))
        {
            return null;
        }

        return $this->processUserData($data);
    }

    /**
     * Get total accounts that are online.
     */
    public function getCountAcctsOnline(int $timeout): int
    {
        $qb = $this->createQueryBuilder('u');

        try
        {
            $date = new DateTime('now');
            $date->sub(new DateInterval("PT{$timeout}S"));

            return (int) $qb->select('COUNT(1)')
                ->where('u.locked = 0 AND u.loggedin = 1 AND u.laston > :date')
                ->setParameter('date', $date)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }

    /**
     * Get list of accounts online.
     */
    public function getListAccountsOnline(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query
                ->select('u.login')
                ->select('c.name')
                ->leftJoin('LotgdCore:Avatar', 'c', 'with', $query->expr()->eq('c.acct', 'u.acctid'))
                ->where('u.loggedin = 1 AND u.locked = 0')
                ->orderBy('c.level', Criteria::DESC)

                ->getQuery()
                ->getArrayResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Private function to process data of user.
     *
     * @return array
     */
    private function processUserData(array $data)
    {
        $hydrator = new ClassMethodsHydrator();

        $account   = $hydrator->extract($data[0]);
        $character = $hydrator->extract($account['character']);

        if ( ! $data[1])
        {
            $data[1] = new LotgdEntity\AccountsEverypage();
            $data[1]->setAcctid($account['acctid']);
        }

        $everypage = $hydrator->extract($data[1]);

        $character['character_id'] = $character['id'];

        unset($account['character'], $character['acct']);

        return array_merge($account, $character, $everypage);
    }
}
