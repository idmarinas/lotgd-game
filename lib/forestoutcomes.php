<?php

// addnews ready
// translator ready
// mail ready

use Lotgd\Core\Event\Creature;

require_once 'lib/creaturefunctions.php';

/**
 * Buff creature for optimize to character stats.
 *
 * @param array  Information of creature
 * @param string $hook   Hook to activate when buff badguy
 * @param mixed  $badguy
 *
 * @return array
 */
function buffbadguy($badguy, $hook = 'buffbadguy')
{
    global $session;

    $badguy = lotgd_transform_creature($badguy, false);

    $expflux = \round($badguy['creatureexp'] / 10, 0);
    $expflux = e_rand(-$expflux, $expflux);
    $badguy['creatureexp'] += $expflux;

    if (getsetting('disablebonuses', 1))
    {
        //adapting flux as for people with many DKs they will just bathe in gold....
        $base = 30 - \min(20, \round(\sqrt($session['user']['dragonkills']) / 2));
        $base /= 1000;
        $bonus = 1 + $base * ($badguy['creatureattackattrs'] + $badguy['creaturedefenseattrs']) + .001 * $badguy['creaturehealthattrs'];

        $badguy['creaturegold'] = \round($badguy['creaturegold'] * $bonus, 0);
        $badguy['creatureexp']  = \round($badguy['creatureexp'] * $bonus, 0);
    }

    //-- Activate hook when find a creature
    $badguy = new Creature($badguy);
    \LotgdEventDispatcher::dispatch($badguy, Creature::ENCOUNTER);
    $badguy->setData(modulehook('creatureencounter', $badguy->getData()));

    $hookNew = $hook;

    if ('buffbadguy' == $hook)
    {
        $hookNew = 'badguy';
    }
    elseif ('buffmaster' == $hook)
    {
        $hookNew = 'master';
    }
    //-- Activate hook custom or default (buffbadguy)
    \LotgdEventDispatcher::dispatch($badguy, Creature::BUFF_FOR.$hookNew);
    $badguy = modulehook($hook, $badguy->getData());

    //-- Update max creature health
    $badguy['creaturemaxhealth'] = $badguy['creaturehealth'];

    lotgd_show_debug_creature($badguy);

    return $badguy;
}
