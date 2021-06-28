<?php

// addnews ready
// translator ready
// mail ready

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.date_time')->checkDay()" instead. Removed in future versions. */
function checkday()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.date_time")->checkDay()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.date_time')->isNewDay()" instead. Removed in future versions. */
function is_new_day(): bool
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.date_time")->isNewDay()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.date_time')->isNewDay();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.date_time')->getGameTime()" instead. Removed in future versions. */
function getgametime()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.date_time")->getGameTime()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.date_time')->getGameTime();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.date_time')->gameTime()" instead. Removed in future versions. */
function gametime()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.date_time")->gameTime()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.date_time')->gameTime();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.date_time')->convertGameTime($intime, $debug)" instead. Removed in future versions. */
function convertgametime(int $intime, $debug = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.date_time")->convertGameTime($intime, $debug)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.date_time')->convertGameTime($intime, (bool) $debug);
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.date_time')->gameTimeDetails()" instead. Removed in future versions. */
function gametimedetails()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.date_time")->gameTimeDetails()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.date_time')->gameTimeDetails();
}

/** @deprecated 5.5.0 use "LotgdKernel::get('lotgd_core.tool.date_time')->secondsToNextGameDay($details)" instead. Removed in future versions. */
function secondstonextgameday($details = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.5.0; and delete in future version. Use "LotgdKernel::get("lotgd_core.tool.date_time")->secondsToNextGameDay($details)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.date_time')->secondsToNextGameDay($details ?: null);
}
