<?php

// addnews ready
// translator ready
// mail ready

use Lotgd\Core\Event\Other;

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

    $args = new Other(['text' => $text, 'type' => $type]);
    \LotgdEventDispatcher::dispatch($args, Other::SPECIAL_HOLIDAY);
    $args = modulehook('holiday', $args->getData());

    return $args['text'];
}
