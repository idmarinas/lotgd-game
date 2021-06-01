<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated  5.3.0 Removed in future versions.*/
function checkban($login = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.3.0; and delete in future version. Use "LotgdTool::checkBan($login);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::checkBan($login ?: null);
}
