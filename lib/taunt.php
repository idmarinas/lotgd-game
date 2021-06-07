<?php

// translator ready
// addnews ready
// mail ready

/**
 * Select 1 taunt.
 *
 * @param array $extraParams
 */
function select_taunt($extraParams = []): array
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.4.0; and delete in future version. Use "LotgdTool::selectTaunt($extraParams);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::selectTaunt($extraParams);
}
