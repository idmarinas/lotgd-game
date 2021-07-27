<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 6.0.0 Use LotgdKernel::get("Lotgd\Core\Pvp\Support")->setupPvpTarget($characterId) instead */
function setup_pvp_target(int $characterId)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use LotgdKernel::get("Lotgd\Core\Pvp\Support")->setupPvpTarget($characterId) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return LotgdKernel::get('Lotgd\Core\Pvp\Support')->setupPvpTarget($characterId);
}

/** @deprecated 6.0.0 Use LotgdKernel::get("Lotgd\Core\Pvp\Support")->pvpVictory($badguy, $killedloc) instead */
function pvpvictory($badguy, $killedloc)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use LotgdKernel::get("Lotgd\Core\Pvp\Support")->pvpVictory($badguy, $killedloc) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return LotgdKernel::get('Lotgd\Core\Pvp\Support')->pvpVictory($badguy, $killedloc);
}

/** @deprecated 6.0.0 Use LotgdKernel::get("Lotgd\Core\Pvp\Support")->pvpDefeat($badguy, $killedloc) instead */
function pvpdefeat($badguy, $killedloc)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use LotgdKernel::get("Lotgd\Core\Pvp\Support")->pvpDefeat($badguy, $killedloc) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return LotgdKernel::get('Lotgd\Core\Pvp\Support')->pvpDefeat($badguy, $killedloc);
}
