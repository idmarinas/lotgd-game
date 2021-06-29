<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.player_functions')->incrementSpecialty($colorcode, $spec)" instead. Removed in future versions. */
function increment_specialty($colorcode, $spec = false)
{
    \LotgdKernel::get('lotgd_core.tool.player_functions')->incrementSpecialty($colorcode, $spec ?: null);
}
