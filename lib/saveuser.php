<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 5.4.0 Migrated to service Lotgd\Core\Tool\Tool, deleted in future version. */
function saveuser($update_last_on = true)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.4.0; and delete in future version. Use "LotgdTool::saveUser($update_last_on);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdTool::saveUser($update_last_on);
}
