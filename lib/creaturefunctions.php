<?php

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdGenerateCreatureLevels($level)" instead. Removed in future versions. */
function lotgd_generate_creature_levels($level = null)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.creature_functions")->lotgdGenerateCreatureLevels($level)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdGenerateCreatureLevels($level);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdGenerateDoppelganger($level)" instead. Removed in future versions. */
function lotgd_generate_doppelganger(int $level): array
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.creature_functions")->lotgdGenerateDoppelganger($level)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdGenerateDoppelganger($level);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdTransformCreature($badguy, $debug)" instead. Removed in future versions. */
function lotgd_transform_creature(array $badguy, $debug = true)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.creature_functions")->lotgdTransformCreature($badguy, $debug)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdTransformCreature($badguy, (bool) $debug);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdSearchCreature($multi, $targetlevel, $mintargetlevel, $packofmonsters, $forest)" instead. Removed in future versions. */
function lotgd_search_creature($multi, $targetlevel, $mintargetlevel, $packofmonsters = false, $forest = true): array
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.creature_functions")->lotgdSearchCreature($multi, $targetlevel, $mintargetlevel, $packofmonsters, $forest)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdSearchCreature($multi, $targetlevel, $mintargetlevel, $packofmonsters, $forest);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureStats($dk)" instead. Removed in future versions. */
function get_creature_stats($dk = 0)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.creature_functions")->getCreatureStats($dk)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureStats($dk);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureHitpoints($attrs)" instead. Removed in future versions. */
function get_creature_hitpoints($attrs)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.creature_functions")->getCreatureHitpoints($attrs)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureHitpoints($attrs);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureAttack($attrs)" instead. Removed in future versions. */
function get_creature_attack($attrs)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.creature_functions")->getCreatureAttack($attrs)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureAttack($attrs);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureDefense($attrs)" instead. Removed in future versions. */
function get_creature_defense($attrs)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.creature_functions")->getCreatureDefense($attrs)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureDefense($attrs);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureSpeed($attrs)" instead. Removed in future versions. */
function get_creature_speed($attrs)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.creature_functions")->getCreatureSpeed($attrs)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureSpeed($attrs);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdShowDebugCreature($badguy)" instead. Removed in future versions. */
function lotgd_show_debug_creature(iterable $badguy)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.creature_functions")->lotgdShowDebugCreature($badguy)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdShowDebugCreature($badguy);
}
