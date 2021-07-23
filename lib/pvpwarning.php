<?php

// addnews ready
// translator ready
// mail ready

/** @deprecated 6.0.0 use LotgdKernel::get('Lotgd\Core\Pvp\Warning')->warning(); */
function pvpwarning($dokill = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use LotgdKernel::get("Lotgd\Core\Pvp\Warning")->warning() instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('Lotgd\Core\Pvp\Warning')->warning($dokill);
}
