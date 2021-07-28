<?php

/** @deprecated 6.0.0 deleted in 7.0.0 version. Use "LotgdKernel::get('lotgd_core.tool.staff')->killPlayer($explossproportion, $goldlossproportion)" instead */
function killplayer($explossproportion = 0.1, $goldlossproportion = 1)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use "LotgdKernel::get("lotgd_core.tool.staff")->killPlayer($explossproportion, $goldlossproportion)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdKernel::get('lotgd_core.tool.staff')->killPlayer($explossproportion, $goldlossproportion);
}
