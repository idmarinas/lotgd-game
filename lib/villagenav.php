<?php

// translator ready
// addnews ready
// mail ready
function villagenav($extra = false)
{
    global $session;
    $loc = $session['user']['location'];

    if (false === $extra)
    {
        $extra = '';
    }
    $args = modulehook('villagenav');

    if (array_key_exists('handled', $args) && $args['handled'])
    {
        return;
    }
    tlschema('nav');

    if ($session['user']['alive'])
    {
        LotgdNavigation::addNav('V?Return to %s', "village.php$extra", ['params' => ['location' => $loc]]);
    }
    else
    {
        // user is dead
        addnav('S?Return to the Shades', 'shades.php');
    }
    tlschema();
}
