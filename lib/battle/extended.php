<?php

// addnews ready
// mail ready
// translation ready
//

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->prepareDataBattleBars($enemies)" instead. Removed in future version. */
function prepare_data_battlebars(array $enemies)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->prepareDataBattleBars($enemies)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.battle')->prepareDataBattleBars($enemies);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->prepareFight($options)" instead. Removed in future version. */
function prepare_fight($options = [])
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->prepareFight($options)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.battle')->prepareFight($options);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->prepareCompanions()" instead. Removed in future version. */
function prepare_companions()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->prepareCompanions()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->prepareCompanions();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->suspendCompanions($susp, $nomsg)" instead. Removed in future version. */
function suspend_companions($susp, $nomsg = null)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->suspendCompanions($susp, $nomsg)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->suspendCompanions($susp, $nomsg);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->unSuspendCompanions($susp, $nomsg)" instead. Removed in future version. */
function unsuspend_companions($susp, $nomsg = null)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->unSuspendCompanions($susp, $nomsg)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->unSuspendCompanions($susp, $nomsg);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->autoSetTarget($localenemies)" instead. Removed in future version. */
function autosettarget($localenemies)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->autoSetTarget($localenemies)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.battle')->autoSetTarget($localenemies);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->reportCompanionMove($companion, $activate)" instead. Removed in future version. */
function report_companion_move($companion, $activate = 'fight')
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->reportCompanionMove($companion, $activate)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.battle')->reportCompanionMove($companion, $activate);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->rollCompanionDamage($companion)" instead. Removed in future version. */
function rollcompaniondamage($companion)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->rollCompanionDamage($companion)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.battle')->rollCompanionDamage($companion);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->battleSpawn($creature)" instead. Removed in future version. */
function battle_spawn($creature)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->battleSpawn($creature)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->battleSpawn($creature);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->battleHeal($amount, $target)" instead. Removed in future version. */
function battle_heal($amount, $target = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->battleHeal($amount, $target)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->battleHeal($amount, $target);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->executeAiScript($script)" instead. Removed in future version. */
function execute_ai_script($script)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->executeAiScript($script)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->executeAiScript($script);
}
