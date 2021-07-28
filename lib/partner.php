<?php

/** @deprecated 6.0.0 deleted in 7.0.0 version. Use "LotgdTool::getPartner(bool $player)" */
function get_partner($player = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use "LotgdTool::getPartner(bool $player);" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdTool::getPartner($player);
}
