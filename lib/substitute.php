<?php

// translator ready
// addnews ready
// mail ready

/** @deprecated 5.4.0 Use "LotgdTool::substitute($string, $extraSearch, $extraReplace);" instead. Removed in future versions. */
function substitute($string, $extra = null, $extrarep = null)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.4.0; and delete in future version. Use "LotgdTool::substitute($string, $extraSearch, $extraReplace);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);


    return \LotgdTool::substitute($string, $extra ?: null, $extrarep ?: null);
}

/** @deprecated 5.4.0 Use "LotgdTool::substituteArray($string, $extraSearch, $extraReplace);" instead. Removed in future versions. */
function substitute_array($string, $extra = null, $extrarep = null)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.4.0; and delete in future version. Use "LotgdTool::substituteArray($string, $extraSearch, $extraReplace);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::substituteArray($string, $extra ?: null, $extrarep ?: null);
}
