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

use Lotgd\Core\Event\Fight;
use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

trait Menu
{
    public function fightNav(bool $allowspecial = true, bool $allowflee = true, ?string $script = null)
    {
        global $session, $newenemies;

        //-- Change text domain for navigation
        $this->setTextDomain('navigation_fightnav');

        if ( ! $script)
        {
            $PHP_SELF = $this->request->getServer('PHP_SELF');
            $script   = \substr($PHP_SELF, \strrpos($PHP_SELF, '/') + 1).'?';
        }
        elseif (false === \strpos($script, '?'))
        {
            $script .= '?';
        }
        elseif ('&' != \substr($script, \strlen($script) - 1))
        {
            $script .= '&';
        }

        $fight = $session['user']['alive'] ? 'nav.fight.live' : 'nav.fight.death';
        $run   = $session['user']['alive'] ? 'nav.run.live' : 'nav.run.death';

        $args = new Fight(['script' => $script]);
        $this->dispatcher->dispatch($args, Fight::NAV_PRE);
        modulehook('fightnav-prenav', $args->getData());

        $this->addHeader('category.standard');
        $this->addNav($fight, "{$script}op=fight");

        if ($allowflee)
        {
            $this->addNav($run, "{$script}op=run");
        }

        if ($session['user']['superuser'] & SU_DEVELOPER)
        {
            $this->addNav('nav.abort', $script);
        }

        if ($this->settings->getSetting('autofight', 0))
        {
            $this->addHeader('category.automatic');
            $this->addNav('nav.auto.05', "{$script}op=fight&auto=five");
            $this->addNav('nav.auto.010', "{$script}op=fight&auto=ten");
            $auto = $this->settings->getSetting('autofightfull', 0);

            if ((1 == $auto || (2 == $auto && ! $allowflee)) && 1 == \count($newenemies))
            {
                $this->addNav('nav.auto.end', "{$script}op=fight&auto=full");
            }
            elseif (1 == $auto || (2 == $auto && ! $allowflee))
            {
                $this->addNav('nav.auto.current', "{$script}op=fight&auto=full");
            }
        }

        //added hook for the Stamina system
        if ( ! $session['user']['alive'])
        {
            $this->dispatcher->dispatch($args, Fight::NAV_GRAVEYARD);
            modulehook('fightnav-graveyard', $args->getData());
        }

        if ($allowspecial)
        {
            $this->addHeader('category.special', ['hiddeEmpty' => false]);

            $this->dispatcher->dispatch($args, Fight::NAV_SPECIALTY);
            modulehook('fightnav-specialties', $args->getData());

            if ($session['user']['superuser'] & SU_DEVELOPER)
            {
                $this->addHeader('category.superuser');
                $this->addNav('nav.god', "{$script}op=fight&skill=godmode");
            }

            $this->dispatcher->dispatch($args, Fight::NAV);
            modulehook('fightnav', $args->getData());
        }

        if (\count($newenemies) > 1)
        {
            $this->addHeader('category.target');

            foreach ($newenemies as $index => $badguy)
            {
                if ($badguy['creaturehealth'] <= 0 || (isset($badguy['dead']) && $badguy['dead']))
                {
                    continue;
                }

                $this->addNavNotl(
                    (($badguy['istarget'] ?? false) ? '`#*`0' : '').$badguy['creaturename'],
                    "{$script}op=fight&newtarget={$index}"
                );
            }
        }

        //-- Restore text domain for navigation
        $this->setTextDomain();
    }

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
}
