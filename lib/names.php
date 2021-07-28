<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 6.0.0 deleted in 7.0.0 version. Use "LotgdTool::getPlayerTitle($old)" instead */
function get_player_title($old = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use "LotgdTool::getPlayerTitle($old)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::getPlayerTitle($old);
}

/** @deprecated 6.0.0 deleted in 7.0.0 version. Use "LotgdTool::getPlayerBasename($old)" instead */
function get_player_basename($old = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use "LotgdTool::getPlayerBasename($old)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::getPlayerBasename($old);
}

/** @deprecated 6.0.0 deleted in 7.0.0 version. Use "LotgdTool::changePlayerName($newname, $old)" instead */
function change_player_name($newname, $old = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use "LotgdTool::changePlayerName($newname, $old)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::changePlayerName($newname, $old);
}

/** @deprecated 6.0.0 deleted in 7.0.0 version. Use "LotgdTool::changePlayerCtitle($nctitle, $old)" instead */
function change_player_ctitle($nctitle, $old = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use "LotgdTool::changePlayerCtitle($nctitle,$old)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::changePlayerCtitle($nctitle, $old);
}

/** @deprecated 6.0.0 deleted in 7.0.0 version. Use "LotgdTool::changePlayerTitle($ntitle, $old)" instead */
function change_player_title($ntitle, $old = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use "LotgdTool::changePlayerTitle($ntitle, $old)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::changePlayerTitle($ntitle, $old);
}
