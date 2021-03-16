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

namespace Lotgd\Bundle\CoreBundle\EventSubscriber;

use Lotgd\Bundle\SettingsBundle\Event\SettingsEvent;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Lotgd\Bundle\SettingsBundle\LotgdSettingCache;

class SettingsSubscriber implements EventSubscriberInterface
{
    protected $cache;

    public function __construct(CacheItemPoolInterface $lotgdCorePackageCache)
    {
        $this->cache = $lotgdCorePackageCache;
    }

    public function onCreateSetting(SettingsEvent $event)
    {
        $setting = $event->getSetting();

        $item = $this->cache->getItem(LotgdSettingCache::COLLECTION);
        /** @var ArrayCollection */
        $collection = $item->get();

        if ( ! $collection)
        {
            return;
        }

        $collection->add($setting);

        $item->set($collection);
        $this->cache->save($item);
    }

    public function onDeleteSetting(SettingsEvent $event)
    {
        $setting = $event->getSetting();

        $item = $this->cache->getItem(LotgdSettingCache::COLLECTION);
        /** @var ArrayCollection */
        $collection = $item->get();

        if ( ! $collection)
        {
            return;
        }

        $collection->removeElement($setting);

        $item->set($collection);
        $this->cache->save($item);
    }

    public function onUpdateSetting(SettingsEvent $event)
    {
        $setting = $event->getSetting();

        $item = $this->cache->getItem(LotgdSettingCache::COLLECTION);
        /** @var ArrayCollection */
        $collection = $item->get();

        if ( ! $collection)
        {
            return;
        }

        if ($collection->contains($setting))
        {
            $index = $collection->indexOf($setting);

            $collection->set($index, $setting);
        }

        $item->set($collection);
        $this->cache->save($item);
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SettingsEvent::POST_CREATE_SETTING => 'onCreateSetting',
            SettingsEvent::POST_DELETE_SETTING => 'onDeleteSetting',
            SettingsEvent::POST_UPDATE_SETTING => 'onUpdateSetting',
        ];
    }
}
