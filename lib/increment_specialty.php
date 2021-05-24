<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Event\Character;

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
        $args = new Character(['color' => $colorcode]);
        \LotgdEventDispatcher::dispatch($args, Character::SPECIALTY_INCREMENT);
        modulehook('incrementspecialty', $args->getData());
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
