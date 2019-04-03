<?php

// translator ready
// addnews ready
// mail ready
function superusernav()
{
    global $session;

    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use "LotgdNavigation::superuserGrottoNav()".',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdNavigation::superuserGrottoNav();
}
