<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->rollDamage()" instead. Removed in future version. */
function rolldamage()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->rollDamage()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.battle')->rollDamage();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->reportPowerMove($crit, $dmg)" instead. Removed in future version. */
function report_power_move($crit, $dmg)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->reportPowerMove($crit, $dmg)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.battle')->reportPowerMove($crit, $dmg);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->suspendBuffs($susp, $msg)" instead. Removed in future version. */
function suspend_buffs($susp = false, $msg = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->suspendBuffs($susp, $msg)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->suspendBuffs($susp, $msg);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->suspendBuffByName($name, $msg)" instead. Removed in future version. */
function suspend_buff_by_name($name, $msg = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->suspendBuffByName($name, $msg)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->suspendBuffByName($name, $msg);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->unsuspendBuffByName($name, $msg)" instead. Removed in future version. */
function unsuspend_buff_by_name($name, $msg = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->unsuspendBuffByName($name, $msg)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->unsuspendBuffByName($name, $msg);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->isBuffActive($name)" instead. Removed in future version. */
function is_buff_active($name)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->isBuffActive($name)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.battle')->isBuffActive($name);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->unsuspendBuffs($susp, $msg)" instead. Removed in future version. */
function unsuspend_buffs($susp = false, $msg = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->unsuspendBuffs($susp, $msg)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->unsuspendBuffs($susp, $msg);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->applyBodyguard($level)" instead. Removed in future version. */
function apply_bodyguard($level)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->applyBodyguard($level)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->applyBodyguard($level);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->applySkill($skill, $l)" instead. Removed in future version. */
function apply_skill($skill, $l)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->applySkill($skill, $l)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->applySkill($skill, $l);
}
