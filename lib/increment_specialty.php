<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.player_functions')->incrementSpecialty($colorcode, $spec)" instead. Removed in future versions. */
function increment_specialty($colorcode, $spec = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.player_functions")->incrementSpecialty($colorcode, $spec)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.tool.player_functions')->incrementSpecialty($colorcode, $spec ?: null);
}
