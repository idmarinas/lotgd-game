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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Bundle\SettingsBundle\Entity\Setting;
use Lotgd\Bundle\SettingsBundle\LotgdSettingCache;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * @method Setting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Setting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Setting[]    findAll()
 * @method Setting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingRepository extends ServiceEntityRepository
{
    private $cache;

    public function __construct(ManagerRegistry $registry, TagAwareCacheInterface $lotgdCorePackageCache)
    {
        parent::__construct($registry, Setting::class);

        $this->cache = $lotgdCorePackageCache;
    }

    public function getSettingObject(string $name, ?UserInterface $user = null): ?Setting
    {
        $collection = $this->getCollectionSettings();

        $setting = $collection->filter(function (Setting $item) use ($name, $user)
        {
            return $item->getName() == $name && $this->checkUserIsEqual($item->getUser(), $user);
        })->first();

        return $setting ?: null;
    }

    /**
     * @param mixed      $name
     * @param mixed|null $default
     *
     * @return string|int|float|bool|null
     */
    public function getSetting(string $name, ?UserInterface $user = null, $default = null)
    {
        $setting = $this->getSettingObject($name, $user);

        return $setting ? $setting->formatedValue() : $default;
    }

    /**
     * @param mixed|null $default
     *
     * @return string|int|float|bool|null
     */
    public function getSettingByDomain(string $domain, string $name, ?UserInterface $user = null, $default = null)
    {
        $collection = $this->getCollectionSettings();

        $setting = $collection->filter(function (Setting $item) use ($domain, $name, $user)
        {
            return $item->getDomain()->getName() == $domain && $item->getName() == $name && $this->checkUserIsEqual($item->getUser(), $user);
        })->first();

        return $setting ? $setting->formatedValue() : $default;
    }

    private function getCollectionSettings(): ?ArrayCollection
    {
        return $this->cache->get(LotgdSettingCache::COLLECTION, function (ItemInterface $item)
        {
            $item->tag(['lotgd_settings_bundle']);

            //-- Fetch associations too
            $query = $this->_em->createQuery(
                'SELECT p, d, u
                FROM LotgdSettings:Setting p
                LEFT JOIN p.domain d
                LEFT JOIN p.user u
                ORDER BY p.domain ASC'
            );

            $collection = $query->getResult();
            foreach ($collection as &$row)
            {
                if ($row->getUser())
                {
                    $row->getUser()->eraseDataForCache();
                }
            }

            $collection = new ArrayCollection($collection ?: []);

            //-- If is empty expire after 30 seconds to reload
            if ( ! $collection->count())
            {
                $item->expiresAfter(30);
            }

            return $collection;
        });
    }

    private function checkUserIsEqual($itemUser, $user): bool
    {
        return (bool) ($itemUser == $user
            || ($itemUser && $user && $itemUser->getId() == $user->getId())
        );
    }
}
