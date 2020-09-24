<?php

// translator ready
// addnews ready
// mail ready

function increment_specialty($colorcode, $spec = false)
{
    global $session;

    if (false !== $spec)
    {
        $revertspec                   = $session['user']['specialty'];
        $session['user']['specialty'] = $spec;
    }

    if ('' != $session['user']['specialty'])
    {
        $args = ['color' => $colorcode];
        \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CHARACTER_SPECIALTY_INCREMENT, null, $args);
        modulehook('incrementspecialty', $args);
    }
    else
    {
        output('`7You have no direction in the world, you should rest and make some important decisions about your life.`0`n');
    }

    if (false !== $spec)
    {
        $session['user']['specialty'] = $revertspec;
    }
}
