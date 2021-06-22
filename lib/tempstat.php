<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.temp_stats')->applyTempStat($name, $value, $type)" instead. Removed in future versions. */
function apply_temp_stat($name, $value, $type = 'add')
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.temp_stats")->applyTempStat($name, $value, $type)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.temp_stats')->applyTempStat($name, $value, $type);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat($name, $color)" instead. Removed in future versions. */
function check_temp_stat($name, $color = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.temp_stats")->checkTempStat($name, $color)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat($name, $color);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.temp_stats')->suspendTempStats()" instead. Removed in future versions. */
function suspend_temp_stats()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.temp_stats")->suspendTempStats()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.temp_stats')->suspendTempStats();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.temp_stats')->restoreTempStats()" instead. Removed in future versions. */
function restore_temp_stats()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.temp_stats")->restoreTempStats()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.temp_stats')->restoreTempStats();
}
