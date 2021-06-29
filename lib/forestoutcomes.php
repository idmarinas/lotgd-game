<?php

// addnews ready
// translator ready
// mail ready

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.creature_functions')->buffBadguy($badguy, $hook)" instead. Removed in future versions. */
function buffbadguy($badguy, $hook = 'buffbadguy')
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.creature_functions")->buffBadguy($badguy, $hook)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.creature_functions')->buffBadguy($badguy, $hook);
}
