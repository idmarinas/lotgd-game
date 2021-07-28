<?php

// addnews ready
// translator ready
// mail ready

/** @deprecated 6.0.0 deleted in 7.0.0 version. Use "LotgdTool::holidayize($text, $type)" */
function holidayize($text, $type = 'unknown')
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use "LotgdTool::holidayize($text, $type)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::holidayize($text, $type);
}
