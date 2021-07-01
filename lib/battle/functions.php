<?php

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->battlePlayerAttacks()" instead. Removed in future version. */
function battle_player_attacks()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->battlePlayerAttacks()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.battle')->battlePlayerAttacks();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->battleBadguyAttacks()" instead. Removed in future version. */
function battle_badguy_attacks()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->battleBadguyAttacks()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.combat.battle')->battleBadguyAttacks();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->battleVictory($enemies, $denyflawless, $forest)" instead. Removed in future version. */
function battlevictory($enemies, $denyflawless = false, $forest = true)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->battleVictory($enemies, $denyflawless, $forest)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->battleVictory($enemies, $denyflawless, $forest);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->battleGainExperienceForest()" instead. Removed in future version. */
function battlegainexperienceforest()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->battleGainExperienceForest()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->battleGainExperienceForest();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->battleGainExperienceGraveyard()" instead. Removed in future version. */
function battlegainexperiencegraveyard()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->battleGainExperienceGraveyard()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->battleGainExperienceGraveyard();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->battleDefeat($enemies, $where, $candie, $lostexp, $lostgold)" instead. Removed in future version. */
function battledefeat($enemies, $where = 'forest', $candie = true, $lostexp = true, $lostgold = true)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->battleDefeat($enemies, $where, $candie, $lostexp, $lostgold)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->battleDefeat($enemies, $where, $candie, $lostexp, $lostgold);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.combat.battle')->battleShowResults($lotgdBattleContent)" instead. Removed in future version. */
function battleshowresults(array $lotgdBattleContent)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.combat.battle")->battleShowResults($lotgdBattleContent)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.combat.battle')->battleShowResults($lotgdBattleContent);
}
