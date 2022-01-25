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

namespace Lotgd\Core\Combat\Battle;

use Lotgd\Core\Event\Fight;

trait Menu
{
    public function fightNav(bool $allowspecial = true, bool $allowflee = true, ?string $script = null)
    {
        //-- Change text domain for navigation
        $this->navigation->setTextDomain('navigation_fightnav');

        if ( ! $script)
        {
            $PHP_SELF = $this->request->getServer('PHP_SELF');
            $script   = substr($PHP_SELF, strrpos($PHP_SELF, '/') + 1).'?';
        }
        elseif (false === strpos($script, '?'))
        {
            $script .= '?';
        }
        elseif ('&' != substr($script, \strlen($script) - 1))
        {
            $script .= '&';
        }

        $fight = $this->user['alive'] ? 'nav.fight.live' : 'nav.fight.death';
        $run   = $this->user['alive'] ? 'nav.run.live' : 'nav.run.death';

        $args = new Fight(['script' => $script]);
        $this->dispatcher->dispatch($args, Fight::NAV_PRE);
        modulehook('fightnav-prenav', $args->getData());

        $this->navigation->addHeader('category.standard');
        $this->navigation->addNav($fight, "{$script}op=fight");

        if ($allowflee)
        {
            $this->navigation->addNav($run, "{$script}op=run");
        }

        ($this->user['superuser'] & SU_DEVELOPER) && $this->navigation->addNav('nav.abort', $script);

        if ($this->settings->getSetting('autofight', 0))
        {
            $this->navigation->addHeader('category.automatic');
            $this->navigation->addNav('nav.auto.05', "{$script}op=fight&auto=five");
            $this->navigation->addNav('nav.auto.010', "{$script}op=fight&auto=ten");
            $auto = $this->settings->getSetting('autofightfull', 0);

            if ((1 == $auto || (2 == $auto && ! $allowflee)) && 1 == $this->countEnemiesAlive())
            {
                $this->navigation->addNav('nav.auto.end', "{$script}op=fight&auto=full");
            }
            elseif (1 == $auto || (2 == $auto && ! $allowflee))
            {
                $this->navigation->addNav('nav.auto.current', "{$script}op=fight&auto=full");
            }
        }

        //added hook for the Stamina system
        if ( ! $this->user['alive'])
        {
            $this->dispatcher->dispatch($args, Fight::NAV_GRAVEYARD);
            modulehook('fightnav-graveyard', $args->getData());
        }

        if ($allowspecial)
        {
            $this->navigation->addHeader('category.special', ['hiddeEmpty' => false]);

            $this->dispatcher->dispatch($args, Fight::NAV_SPECIALTY);
            modulehook('fightnav-specialties', $args->getData());

            if (($this->user['superuser'] & SU_DEVELOPER) !== 0)
            {
                $this->navigation->addHeader('category.superuser');
                $this->navigation->addNav('nav.god', "{$script}op=fight&skill=godmode");
            }

            $this->dispatcher->dispatch($args, Fight::NAV);
            modulehook('fightnav', $args->getData());
        }

        if ($this->countEnemiesAlive() > 1)
        {
            $this->navigation->addHeader('category.target');

            foreach ($this->enemies as $index => $badguy)
            {
                if ($badguy['creaturehealth'] <= 0 || (isset($badguy['dead']) && $badguy['dead']))
                {
                    continue;
                }

                $this->navigation->addNavNotl(
                    (($badguy['istarget'] ?? false) ? '`#*`0' : '').$badguy['creaturename'],
                    "{$script}op=fight&newtarget={$index}"
                );
            }
        }

        //-- Restore text domain for navigation
        $this->navigation->setTextDomain();
    }
}
