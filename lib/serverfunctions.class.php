<?php

class ServerFunctions
{
    public static function isTheServerFull()
    {
        \trigger_error(\sprintf(
            'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use LotgdKernel::get("lotgd_core.service.server_functions")->isTheServerFull() instead or dependency injection.',
            __CLASS__.'::'.__METHOD__
        ), E_USER_DEPRECATED);

        return \LotgdKernel::get('lotgd_core.service.server_functions')->isTheServerFull();
    }

    public static function resetAllDragonkillPoints($acctid = false)
    {
        \trigger_error(\sprintf(
            'Usage of %s is obsolete since 6.0.0; and delete in 7.0.0 version. Use LotgdKernel::get("lotgd_core.service.server_functions")->resetAllDragonkillPoints($acctid) instead or dependency injection.',
            __CLASS__.'::'.__METHOD__
        ), E_USER_DEPRECATED);

        return \LotgdKernel::get('lotgd_core.service.server_functions')->resetAllDragonkillPoints($acctid);
    }
}
