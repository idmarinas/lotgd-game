<?php

// addnews ready
// translator ready
// mail ready

function calculate_buff_fields()
{
    \LotgdKernel::get('lotgd_core.combat.buffs')->calculate_buff_fields();
}//end function

function restore_buff_fields()
{
    \LotgdKernel::get('lotgd_core.combat.buffs')->restore_buff_fields();
}//end function

function apply_buff($name, $buff)
{
    \LotgdKernel::get('lotgd_core.combat.buffs')->apply_buff($name, $buff);
}

function apply_companion($name, $companion, $ignorelimit = false)
{
    return \LotgdKernel::get('lotgd_core.combat.buffs')->apply_companion($name, $companion, $ignorelimit);
}

function strip_buff($name)
{
    \LotgdKernel::get('lotgd_core.combat.buffs')->strip_buff();
}

function strip_all_buffs()
{
    \LotgdKernel::get('lotgd_core.combat.buffs')->strip_all_buffs();
}

function has_buff($name)
{
    return \LotgdKernel::get('lotgd_core.combat.buffs')->has_buff($name);
}
