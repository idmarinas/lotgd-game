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

namespace Lotgd\Bundle\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Lotgd\Bundle\CoreBundle\Event\ConfigureMenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MenuBuilder
{
    private $factory;
    private $eventDispatcher;

    /**
     * Add any other dependency you need...
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory         = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createMenuCore(): ItemInterface
    {
        $menu = $this->factory->createItem('core');
        $menu->setChildrenAttributes([
            'class' => 'navigation',
            'role'  => 'menu'
        ]);

        $menu->addChild('core.header.login');
        $menu['core.header.login']->addChild('core.menu.login', ['route' => 'lotgd_core_home']);

        $menu->addChild('core.header.new');
        $menu['core.header.new']->addChild('core.menu.create', ['route' => 'lotgd_user_register']);

        $menu->addChild('core.header.func');
        $menu['core.header.func']
            ->addChild('core.menu.forgot', ['route' => 'lotgd_user_forgot_password_request'])
            ->addChild('core.menu.warriors', ['route' => 'lotgd_core_home'])
            ->addChild('core.menu.news', ['route' => 'lotgd_core_home'])
        ;

        $menu->addChild('core.header.about');
        $menu['core.header.about']
            ->addChild('core.menu.about', ['route' => 'lotgd_core_about'])
            ->addChild('core.menu.net', ['route' => 'lotgd_core_home'])
        ;

        $menu->addChild('core.header.other');
        $menu['core.header.other']
            ->addChild('core.menu.setup', ['route' => 'lotgd_core_about_game_setup'])
            ->addChild('core.menu.bundle', ['route' => 'lotgd_core_about_bundles'])
        ;

        $event = new ConfigureMenuEvent($this->factory, $menu);
        $this->eventDispatcher->dispatch($event, ConfigureMenuEvent::CORE);

        return $menu;
    }
}
