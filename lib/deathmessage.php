<?php

// translator ready
// addnews ready
// mail ready

/**
 * Select 1 death message.
 *
 * @param string $zone
 * @param array  $extraParams
 * @param array  $extrarep
 *
 * @deprecated 5.4.0 Use "LotgdTool::selectDeathMessage($zone, $extraParams);" instead. Removed in future versions.
 */
function select_deathmessage($zone = 'forest', $extraParams = []): array
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.4.0; and delete in future version. Use "LotgdTool::selectDeathMessage($zone, $extraParams);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::selectDeathMessage($zone, $extraParams);
}
