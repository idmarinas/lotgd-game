<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * Plugin.php - Adapter for the Semantic library.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Menu builder event. Used for extending the menus.
 *
 * @final since sonata-project/admin-bundle 3.52
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class ConfigureMenuEvent extends Event
{
    public const CORE = 'lotgd_core.event.configure.menu.core';

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ItemInterface
     */
    private $menu;

    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu    = $menu;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
