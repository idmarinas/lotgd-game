<?php

// translator ready
// addnews ready
// mail ready
function getmountname()
{
    global $playermount;

    $name = '';
    $lcname = '';

    if (isset($playermount['mountname']))
    {
        $name = sprintf_translate('Your %s', $playermount['mountname']);
        $lcname = sprintf_translate('your %s', $playermount['mountname']);
    }

    if (isset($playermount['newname']) && '' != $playermount['newname'])
    {
        $name = $playermount['newname'];
        $lcname = $playermount['newname'];
    }

    return [$name, $lcname];
}
