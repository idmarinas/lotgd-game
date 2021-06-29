<?php

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerHitpoints($player)" instead. Removed in future versions. */
function get_player_hitpoints($player = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.player_functions")->getPlayerHitpoints($player)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerHitpoints($player ?: null);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.player_functions')->explainedGetPlayerHitpoints($player, $colored)" instead. Removed in future versions. */
function explained_get_player_hitpoints($player = false, $colored = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.player_functions")->explainedGetPlayerHitpoints($player, $colored)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.player_functions')->explainedGetPlayerHitpoints($player ?: null, (bool) $colored);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerAttack($player)" instead. Removed in future versions. */
function get_player_attack($player = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.player_functions")->getPlayerAttack($player)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerAttack($player ?: null);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.player_functions')->explainedRowGetPlayerAttack($player)" instead. Removed in future versions. */
function explained_row_get_player_attack($player = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.player_functions")->explainedRowGetPlayerAttack($player)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.player_functions')->explainedRowGetPlayerAttack($player ?: null);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.player_functions')->explainedGetPlayerAttack($player, $colored)" instead. Removed in future versions. */
function explained_get_player_attack($player = false, $colored = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.player_functions")->explainedGetPlayerAttack($player, $colored)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.player_functions')->explainedGetPlayerAttack($player ?: null, $colored);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerDefense($player)" instead. Removed in future versions. */
function get_player_defense($player = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.player_functions")->getPlayerDefense($player)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerDefense($player ?: null);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.player_functions')->explainedRowGetPlayerDefense($player)" instead. Removed in future versions. */
function explained_row_get_player_defense($player = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.player_functions")->explainedRowGetPlayerDefense($player)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.player_functions')->explainedRowGetPlayerDefense($player ?: null);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.player_functions')->explainedGetPlayerDefense($player, $colored)" instead. Removed in future versions. */
function explained_get_player_defense($player = false, $colored = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.player_functions")->explainedGetPlayerDefense($player, $colored)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.player_functions')->explainedGetPlayerDefense($player ?: null, $colored);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerSpeed($player)" instead. Removed in future versions. */
function get_player_speed($player = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.player_functions")->getPlayerSpeed($player)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerSpeed($player ?: null);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerPhysicalResistance($player)" instead. Removed in future versions. */
function get_player_physical_resistance($player = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.player_functions")->getPlayerPhysicalResistance($player)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerPhysicalResistance($player ?: null);
}
