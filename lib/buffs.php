<?php

// addnews ready
// translator ready
// mail ready

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.buffs')->calculateBuffFields()" instead. Removed in future versions. */
function calculate_buff_fields()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.buffs")->calculateBuffFields()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.buffs')->calculateBuffFields();
}//end function

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.buffs')->restoreBuffFields()" instead. Removed in future versions. */
function restore_buff_fields()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.buffs")->restoreBuffFields()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.buffs')->restoreBuffFields();
}//end function

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.buffs')->applyBuff($name, $buff)" instead. Removed in future versions. */
function apply_buff($name, $buff)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.buffs")->applyBuff($name, $buff)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.buffs')->applyBuff($name, $buff);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.buffs')->applyCompanion($name, $companion, $ignorelimit)" instead. Removed in future versions. */
function apply_companion($name, $companion, $ignorelimit = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.buffs")->applyCompanion($name, $companion, $ignorelimit)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.buffs')->applyCompanion($name, $companion, $ignorelimit);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.buffs')->stripBuff($name)" instead. Removed in future versions. */
function strip_buff($name)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.buffs")->stripBuff($name)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.buffs')->stripBuff($name);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.buffs')->stripAllBuffs()" instead. Removed in future versions. */
function strip_all_buffs()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.buffs")->stripAllBuffs()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.buffs')->stripAllBuffs();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.buffs')->hasBuff($name)" instead. Removed in future versions. */
function has_buff($name)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.buffs")->hasBuff($name)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.buffs')->hasBuff($name);
}
