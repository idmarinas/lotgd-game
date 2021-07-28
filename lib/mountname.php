<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 6.0.0 Delete in version 7.0.0. Not need this for now name of mount. This function is not used by the core. */
function getmountname()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Not need this for now name of mount. This function is not used by the core.',
        __METHOD__
    ), E_USER_DEPRECATED);

    global $playermount;

    $name   = '';
    $lcname = '';

    if (isset($playermount['mountname']))
    {
        $name   = sprintf('Your %s', $playermount['mountname']);
        $lcname = sprintf('your %s', $playermount['mountname']);
    }

    if (isset($playermount['newname']) && '' != $playermount['newname'])
    {
        $name   = $playermount['newname'];
        $lcname = $playermount['newname'];
    }

    return [$name, $lcname];
}
