<?php

// translator ready
// addnews ready
// mail ready
/**
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2009, Dragonprime Development Team
 *
 * @version 1.1.2 Lotgd DragonPrime Edition
 *
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->activateBuffs($tag)" instead. Removed in future version. */
function activate_buffs($tag)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->activateBuffs($tag)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.battle')->activateBuffs($tag);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->processLifeTaps($ltaps, $damage)" instead. Removed in future version. */
function process_lifetaps($ltaps, $damage)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->processLifeTaps($ltaps, $damage)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->processLifeTaps($ltaps, $damage);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->processDmgShield($dshield, $damage)" instead. Removed in future version. */
function process_dmgshield($dshield, $damage)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->processDmgShield($dshield, $damage)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->processDmgShield($dshield, $damage);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->expireBuffs()" instead. Removed in future version. */
function expire_buffs()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->expireBuffs()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->expireBuffs();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->expireBuffsAfterBattle()" instead. Removed in future version. */
function expire_buffs_afterbattle()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->expireBuffsAfterBattle()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->expireBuffsAfterBattle();
}
