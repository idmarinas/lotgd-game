<?php

// translator ready
// addnews ready
// mail ready
function superusernav()
{
    global $session;

    tlschema('nav');
    addnav('Navigation');

    if ($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO)
    {
        $script = substr(httpGetServer('SCRIPT_NAME'), 0, strpos(httpGetServer('SCRIPT_NAME'), '.'));

        if ('superuser' != $script)
        {
            $args = modulehook('grottonav');

            if (! array_key_exists('handled', $args) || ! $args['handled'])
            {
                addnav('G?Return to the Grotto', 'superuser.php');
            }
        }
    }
    $args = modulehook('mundanenav');

    if (! array_key_exists('handled', $args) || ! $args['handled'])
    {
        addnav('M?Return to the Mundane', 'village.php');
    }
    tlschema();
}
