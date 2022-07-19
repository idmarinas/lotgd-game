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

        if (($superuser & SU_EDIT_COMMENTS) !== 0)
        {
            $this->addNav('common.superuser.moderate', 'moderate.php', ['textDomain' => $this->getDefaultTextDomain()]);
        }

        if (($superuser & ~SU_DOESNT_GIVE_GROTTO) !== 0)
        {
            $this->addNav('common.superuser.superuser', 'superuser.php', ['textDomain' => $this->getDefaultTextDomain()]);
        }

        if (($superuser & SU_INFINITE_DAYS) !== 0)
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

        if (($superuser & ~SU_DOESNT_GIVE_GROTTO) !== 0)
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

        $extra = (false === strpos($extra, '?') ? '?' : '');
        $extra = ('?' == $extra ? '' : $extra);

        $args = new GenericEvent();
        $this->dispatcher->dispatch($args, Events::PAGE_NAVIGATION_VILLAGE);
        $args = $args->getArguments();

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

        if ($session['user']['level'] > 1) {
            $this->addNav('nav.slum', 'forest.php?op=search&type=slum', ['textDomain' => $translationDomain]);
        }

        $this->addNav('nav.thrill', 'forest.php?op=search&type=thrill', ['textDomain' => $translationDomain]);

        if ($this->settings->getSetting('suicide', 0) && $this->settings->getSetting('suicidedk', 10) <= $session['user']['dragonkills']) {
            $this->addNav('nav.suicide', 'forest.php?op=search&type=suicide', ['textDomain' => $translationDomain]);
        }

        $this->addHeader('category.other');
    }

    /**
     * Create a navigations navs for graveyard.
     */
    public function graveyardNav(string $translationDomain): void
    {
        global $session;

        $this->addNav('nav.return.shades', 'shades.php', ['textDomain' => $translationDomain]);

        if ($session['user']['gravefights'])
        {
            $this->addHeader('category.torment', ['textDomain' => $translationDomain]);
            $this->addNav('nav.torment', 'graveyard.php?op=search', ['textDomain' => $translationDomain]);
        }

        $this->addHeader('category.places', ['textDomain' => $translationDomain]);
        $this->addNav('nav.warriors', 'list.php', ['textDomain' => $translationDomain]);
        $this->addNav('nav.mausoleum', 'graveyard.php?op=enter', ['textDomain' => $translationDomain]);
    }

    /**
     * Create a navigations navs for gardens.
     */
    public function gardensNav(string $translationDomain): void
    {
        $this->villageNav();
    }

    public function townNav(string $translationDomain): void
    {
        //-- City gates
        $this->addHeader('headers.gate', ['textDomain' => $translationDomain]);
        $this->addNav('navs.forest', 'forest.php', ['textDomain' => $translationDomain]);

        if ($this->settings->getSetting('pvp', 1))
        {
            $this->addNav('navs.pvp', 'pvp.php', ['textDomain' => $translationDomain]);
        }

        //-- Fields
        $this->addHeader('headers.fields', ['textDomain' => $translationDomain]);
        $this->addNav('navs.logout', 'login.php?op=logout', ['textDomain' => $translationDomain]);

        if ($this->settings->getSetting('enablecompanions', true))
        {
            $this->addNav('navs.mercenarycamp', 'mercenarycamp.php', ['textDomain' => $translationDomain]);
        }

        //-- Fight street
        $this->addHeader('headers.fight', ['textDomain' => $translationDomain]);
        $this->addNav('navs.train', 'train.php', ['textDomain' => $translationDomain]);

        if (file_exists('public/lodge.php'))
        {
            $this->addNav('navs.lodge', 'lodge.php', ['textDomain' => $translationDomain]);
        }

        //-- Market street
        $this->addHeader('headers.market', ['textDomain' => $translationDomain]);
        $this->addNav('navs.weaponshop', 'weapons.php', ['textDomain' => $translationDomain]);
        $this->addNav('navs.armorshop', 'armor.php', ['textDomain' => $translationDomain]);
        $this->addNav('navs.bank', 'bank.php', ['textDomain' => $translationDomain]);
        $this->addNav('navs.gypsy', 'gypsy.php', ['textDomain' => $translationDomain]);

        //-- Industrial street
        $this->addHeader('headers.industrial', ['textDomain' => $translationDomain]);

        //-- Tavern street
        $this->addHeader('headers.tavern', ['textDomain' => $translationDomain]);
        $this->addNav('navs.innname', 'inn.php', [
            'textDomain' => $translationDomain,
            'params' => ['inn' => $this->settings->getSetting('innname', LOCATION_INN)]
        ]);
        $this->addNav('navs.stablename', 'stables.php', ['textDomain' => $translationDomain]);
        $this->addNav('navs.gardens', 'gardens.php', ['textDomain' => $translationDomain]);
        $this->addNav('navs.rock', 'rock.php', ['textDomain' => $translationDomain]);

        if ($this->settings->getSetting('allowclans', 1))
        {
            $this->addnav('navs.clan', 'clan.php', ['textDomain' => $translationDomain]);
        }

        //-- Info street
        $this->addHeader('headers.info', ['textDomain' => $translationDomain]);
        $this->addNav('navs.news', 'news.php', ['textDomain' => $translationDomain]);
        $this->addNav('navs.list', 'list.php', ['textDomain' => $translationDomain]);
        $this->addNav('navs.hof', 'hof.php', ['textDomain' => $translationDomain]);

        //-- Other navs
        $this->addHeader('headers.other', ['textDomain' => $translationDomain]);
        $this->addNav('navs.account', 'account.php', ['textDomain' => $translationDomain]);
        $this->addNav('navs.prefs', 'prefs.php', ['textDomain' => $translationDomain]);

        if (! file_exists('public/lodge.php',))
        {
            $this->addNav('navs.referral', 'referral.php', ['textDomain' => $translationDomain] );
        }

        //-- Superuser menu
        $this->superuser();
    }
}
