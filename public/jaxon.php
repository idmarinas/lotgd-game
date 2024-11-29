<?php

use Tracy\Debugger;

\define('OVERRIDE_FORCED_NAV', true);

//-- Allow anonymous connections to Jaxon functions
//-- This avoids some errors and allows to use it with not registers users.
\define('ALLOW_ANONYMOUS', true);

require_once 'common_jaxon.php';

try
{
    $jaxon = LotgdKernel::get('lotgd.core.jaxon');

    if ($jaxon->canProcessRequest())
    {
        $jaxon->processRequest();

        LotgdTool::saveUser(false); //-- Not updated laston (to avoid perma loggedin)

        exit;
    }
}
catch (Throwable $th)
{
    Debugger::log($th);
}
