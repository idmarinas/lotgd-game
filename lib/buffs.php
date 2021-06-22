<?php

// addnews ready
// translator ready
// mail ready

function calculate_buff_fields()
{
    \LotgdKernel::get('lotgd_core.combat.buffs')->calculateBuffFields();
}//end function

function restore_buff_fields()
{
    \LotgdKernel::get('lotgd_core.combat.buffs')->restoreBuffFields();
}//end function

function apply_buff($name, $buff)
{
    \LotgdKernel::get('lotgd_core.combat.buffs')->applyBuff($name, $buff);
}

function apply_companion($name, $companion, $ignorelimit = false)
{
    return \LotgdKernel::get('lotgd_core.combat.buffs')->applyCompanion($name, $companion, $ignorelimit);
}

function strip_buff($name)
{
    \LotgdKernel::get('lotgd_core.combat.buffs')->stripBuff($name);
}

function strip_all_buffs()
{
    \LotgdKernel::get('lotgd_core.combat.buffs')->stripAllBuffs();
}

function has_buff($name)
{
    return \LotgdKernel::get('lotgd_core.combat.buffs')->hasBuff($name);
}
