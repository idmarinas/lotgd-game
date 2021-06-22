<?php

// translator ready
// addnews ready
// mail ready

function apply_temp_stat($name, $value, $type = 'add')
{
    return \LotgdKernel::get('lotgd_core.combat.temp_stats')->applyTempStat($name, $value, $type);
}

function check_temp_stat($name, $color = false)
{
    return \LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat($name, $color);
}

function suspend_temp_stats()
{
    return \LotgdKernel::get('lotgd_core.combat.temp_stats')->suspendTempStats();
}

function restore_temp_stats()
{
    return \LotgdKernel::get('lotgd_core.combat.temp_stats')->restoreTempStats();
}
