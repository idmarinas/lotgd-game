<?php

// translator ready
// addnews ready
// mail ready
function villagenav($extra = false)
{
    global $session;

    if (false === $extra)
    {
        $extra = '';
    }
    $args = modulehook('villagenav');

    if (($args['handled'] ?? false) && $args['handled'])
    {
        return;
    }

    if ($session['user']['alive'])
    {
        LotgdNavigation::addNav('V?Return to {location}', "village.php$extra", ['params' => ['location' => $session['user']['location']]]);

        return;
    }

    // user is dead
    LotgdNavigation::addNav('S?Return to the Shades', 'shades.php');
}
