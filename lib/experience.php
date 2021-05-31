<?php

// translator ready
// addnews ready
// mail ready
// phpDocumentor ready

/**
 * Returns the experience needed to advance to the next level.
 *
 * @param int $curlevel the current level of the player
 * @param int $curdk    the current number of dragonkills
 *
 * @return int the amount of experience needed to advance to the next level
 *
 * @deprecated 5.3.0 Removed in future versions
 */
function exp_for_next_level($curlevel, $curdk)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.3.0; and delete in future version. Use "LotgdTool::expForNextLevel(int $curlevel, int $curdk);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::expForNextLevel($curlevel, $curdk);
}
