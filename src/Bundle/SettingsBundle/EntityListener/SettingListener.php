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

namespace Lotgd\Bundle\SettingsBundle\EntityListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Lotgd\Bundle\SettingsBundle\Entity\Setting;
use Lotgd\Bundle\SettingsBundle\Event\SettingsEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SettingListener
{
    protected $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function prePersist(Setting $setting): void
    {
        $this->eventDispatcher->dispatch(new SettingsEvent($setting), SettingsEvent::PRE_CREATE_SETTING);
    }

    public function postPersist(Setting $setting): void
    {
        $this->eventDispatcher->dispatch(new SettingsEvent($setting), SettingsEvent::POST_CREATE_SETTING);
    }

    public function preUpdate(Setting $setting): void
    {
        $this->eventDispatcher->dispatch(new SettingsEvent($setting), SettingsEvent::PRE_UPDATE_SETTING);
    }

    public function postUpdate(Setting $setting): void
    {
        $this->eventDispatcher->dispatch(new SettingsEvent($setting), SettingsEvent::POST_UPDATE_SETTING);
    }

    public function preRemove(Setting $setting): void
    {
        $this->eventDispatcher->dispatch(new SettingsEvent($setting), SettingsEvent::PRE_DELETE_SETTING);
    }

    public function postRemove(Setting $setting): void
    {
        $this->eventDispatcher->dispatch(new SettingsEvent($setting), SettingsEvent::POST_DELETE_SETTING);
    }
}
