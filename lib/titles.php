<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 6.0.0 deleted in 7.0.0 version. Use "LotgdTool::validDkTitle($title, $dks, $gender)" instead. */
function valid_dk_title($title, $dks, $gender)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use "LotgdTool::validDkTitle($title, $dks, $gender)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::validDkTitle($title, $dks, $gender);
}

/** @deprecated 6.0.0 deleted in 7.0.0 version. Use "LotgdTool::getDkTitle($dks, $gender, $ref)" instead. */
function get_dk_title($dks, $gender, $ref = false)
{
    // $ref is an arbitrary string value.  The title picker will try to
    // give the next highest title in the same 'ref', but if it cannot it'll
    // default to a random one of the ones available for the required DK.

    // Figure out which dk value is the right one to use.. The one to use
    // is the closest one below or equal to the players dk number.
    // We will prefer the dk level from the same $ref if we can, but if there
    // is a closer 'any' match, we will use that!

    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use "LotgdTool::getDkTitle($dks, $gender, $ref)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::getDkTitle($dks, $gender, $ref);
}
