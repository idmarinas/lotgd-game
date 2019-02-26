<?php

// translator ready
// addnews ready
// mail ready
function superusernav()
{
    global $session;

    \LotgdNavigation::addHeader('Navigation');

    if ($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO)
    {
        $script = substr(LotgdHttp::getServer('SCRIPT_NAME'), 0, strpos(LotgdHttp::getServer('SCRIPT_NAME'), '.'));

        if ('superuser' != $script)
        {
            $args = modulehook('grottonav');

            if (! array_key_exists('handled', $args) || ! $args['handled'])
            {
                \LotgdNavigation::addNav('G?Return to the Grotto', 'superuser.php');
            }
        }
    }
    $args = modulehook('mundanenav');

    if (! array_key_exists('handled', $args) || ! $args['handled'])
    {
        \LotgdNavigation::addNav('M?Return to the Mundane', 'village.php');
    }
}
