<?php

// translator ready
// addnews ready
// mail ready
function fightnav($allowspecial = true, $allowflee = true, $script = false)
{
    global $session, $newenemies, $companions;

    //-- Change text domain for navigation
    \LotgdNavigation::setTextDomain('navigation-fightnav');

    if (false === $script)
    {
        $PHP_SELF = LotgdRequest::getServer('PHP_SELF');
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


    $args = ['script' => $script];
    \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_FIGHT_NAV_PRE, null, $args);
    modulehook('fightnav-prenav', $args);

    \LotgdNavigation::addHeader('category.standard');
    \LotgdNavigation::addNav($fight, "{$script}op=fight");

    if ($allowflee)
    {
        \LotgdNavigation::addNav($run, "{$script}op=run");
    }

    if ($session['user']['superuser'] & SU_DEVELOPER)
    {
        \LotgdNavigation::addNav('nav.abort', $script);
    }

    if (getsetting('autofight', 0))
    {
        \LotgdNavigation::addHeader('category.automatic');
        \LotgdNavigation::addNav('nav.auto.05', "{$script}op=fight&auto=five");
        \LotgdNavigation::addNav('nav.auto.010', "{$script}op=fight&auto=ten");
        $auto = getsetting('autofightfull', 0);

        if ((1 == $auto || (2 == $auto && ! $allowflee)) && 1 == \count($newenemies))
        {
            \LotgdNavigation::addNav('nav.auto.end', "{$script}op=fight&auto=full");
        }
        elseif (1 == $auto || (2 == $auto && ! $allowflee))
        {
            \LotgdNavigation::addNav('nav.auto.current', "{$script}op=fight&auto=full");
        }
    }

    //added hook for the Stamina system
    if ( ! $session['user']['alive'])
    {
        \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_FIGHT_NAV_GRAVEYARD, null, $args);
        modulehook('fightnav-graveyard', $args);
    }

    if ($allowspecial)
    {
        \LotgdNavigation::addHeader('category.special', ['hiddeEmpty' => false]);

        \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_FIGHT_NAV_SPECIALTY, null, $args);
        modulehook('fightnav-specialties', $args);

        if ($session['user']['superuser'] & SU_DEVELOPER)
        {
            \LotgdNavigation::addHeader('category.superuser');
            \LotgdNavigation::addNav('nav.god', "{$script}op=fight&skill=godmode");
        }

        \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_FIGHT_NAV, null, $args);
        modulehook('fightnav', $args);
    }

    if (\count($newenemies) > 1)
    {
        \LotgdNavigation::addHeader('category.target');

        foreach ($newenemies as $index => $badguy)
        {
            if ($badguy['creaturehealth'] <= 0 || (isset($badguy['dead']) && $badguy['dead']))
            {
                continue;
            }

            \LotgdNavigation::addNavNotl(
                ($badguy['istarget'] ?? false) ? '`#*`0' : ''.$badguy['creaturename'],
                "{$script}op=fight&newtarget={$index}"
            );
        }
    }

    //-- Restore text domain for navigation
    \LotgdNavigation::setTextDomain();
}
