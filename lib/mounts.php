<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 6.0.0 deleted in version 7.0.0. Use "LotgdTool::getMount($horse)" instead. */
function getmount($horse = 0)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use "LotgdTool::getMount($horse);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::getMount($horse);
}
