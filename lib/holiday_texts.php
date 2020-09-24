<?php

// addnews ready
// translator ready
// mail ready
require_once 'lib/modules.php';

function holidayize($text, $type = 'unknown')
{
    global $session;

    if ( ! isset($session['user']['prefs']['ihavenocheer']))
    {
        $session['user']['prefs']['ihavenocheer'] = 0;
    }

    if ($session['user']['prefs']['ihavenocheer'])
    {
        return $text;
    }

    $args = ['text' => $text, 'type' => $type];
    \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_SPECIAL_HOLIDAY, null, $args);
    $args = modulehook('holiday', $args);

    return $args['text'];
}
