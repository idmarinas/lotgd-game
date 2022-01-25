<?php

use Tracy\Debugger;

\define('OVERRIDE_FORCED_NAV', true);

//-- Allow anonymous conections to Jaxon functions
//-- This avoid some errors and allow to use with not registers users.
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
