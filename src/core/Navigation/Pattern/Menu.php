<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.5.0
 */

namespace Lotgd\Core\Navigation\Pattern;

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

trait Menu
{
    /**
     * Add navs for actions of superuser.
     */
    public function superuser(): void
    {
        global $session;

        $superuser = $session['user']['superuser'];

        $this->addHeader('common.superuser.category', ['textDomain' => $this->getDefaultTextDomain()]);

        if ($superuser & SU_EDIT_COMMENTS)
        {
            $this->addNav('common.superuser.moderate', 'moderate.php', ['textDomain' => $this->getDefaultTextDomain()]);
        }

        if ($superuser & ~SU_DOESNT_GIVE_GROTTO)
        {
            $this->addNav('common.superuser.superuser', 'superuser.php', ['textDomain' => $this->getDefaultTextDomain()]);
        }

        if ($superuser & SU_INFINITE_DAYS)
        {
            $this->addNav('common.superuser.newday', 'newday.php', ['textDomain' => $this->getDefaultTextDomain()]);
        }
    }

    /**
     * Add navs for action of superuser in Grotto page.
     */
    public function superuserGrottoNav(): void
    {
        global $session;

        $superuser = $session['user']['superuser'];

        if ($superuser & ~SU_DOESNT_GIVE_GROTTO)
        {
            $script = $this->request->getServer('SCRIPT_NAME');

            if ('superuser.php' != $script)
            {
                $this->addNav('common.superuser.rsuperuser', 'superuser.php', ['textDomain' => $this->getDefaultTextDomain()]);
            }
        }

        $this->addNav('common.superuser.mundane', 'village.php', ['textDomain' => $this->getDefaultTextDomain()]);
    }

    /**
     * Add nav to village/shades.
     *
     * @param string $extra
     */
    public function villageNav($extra = ''): void
    {
        global $session;

        $extra = (false === \strpos($extra, '?') ? '?' : '');
        $extra = ('?' == $extra ? '' : $extra);

        $args = new GenericEvent();
        $this->dispatcher->dispatch($args, Events::PAGE_NAVIGATION_VILLAGE);
        $args = modulehook('villagenav', $args->getArguments());

        if ($args['handled'] ?? false)
        {
            return;
        }
        elseif ($session['user']['alive'])
        {
            $this->addNav('common.villagenav.village', "village.php{$extra}", [
                'textDomain' => $this->getDefaultTextDomain(),
                'params'     => ['location' => $session['user']['location']],
            ]);

            return;
        }

        //-- User is dead
        $this->addNav('common.villagenav.shades', 'shades.php', ['textDomain' => $this->getDefaultTextDomain()]);
    }

    /**
     * Create a navigations navs for forest.
     *
     * @param string $translationDomain
     * @return void
     */
    public function forestNav(string $translationDomain): void
    {
        global $session;

        $this->addHeader('category.navigation', ['textDomain' => $translationDomain]);
        $this->villageNav();

        $this->addHeader('category.heal', ['textDomain' => $translationDomain]);
        $this->addNav('nav.healer', 'healer.php', ['textDomain' => $translationDomain]);

        $this->addHeader('category.fight', ['textDomain' => $translationDomain]);
        $this->addNav('nav.search', 'forest.php?op=search', ['textDomain' => $translationDomain]);

        ($session['user']['level'] > 1) && $this->addNav('nav.slum', 'forest.php?op=search&type=slum', ['textDomain' => $translationDomain]);

        $this->addNav('nav.thrill', 'forest.php?op=search&type=thrill', ['textDomain' => $translationDomain]);

        ($this->settings->getSetting('suicide', 0) && $this->settings->getSetting('suicidedk', 10) <= $session['user']['dragonkills']) && $this->addNav('nav.suicide', 'forest.php?op=search&type=suicide', ['textDomain' => $translationDomain]);

        $this->addHeader('category.other');
    }
}
