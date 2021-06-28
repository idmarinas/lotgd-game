<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 5.5.0 use "LotgdNavigation::fightNav($allowspecial, $allowflee, $script)" instead. Removed in future versions. */
function fightnav($allowspecial = true, $allowflee = true, $script = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdNavigation::fightNav($allowspecial, $allowflee, $script)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdNavigation::fightNav((bool) $allowspecial, (bool) $allowflee, $script ?: null);
}
