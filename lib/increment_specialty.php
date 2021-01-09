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
        \LotgdResponse::pageAddContent(\LotgdTranslator::t('increment.specialty.none', [], 'app_default'));
    }

    if (false !== $spec)
    {
        $session['user']['specialty'] = $revertspec;
    }
}
