<?php

define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';

if($lotgdJaxon->canProcessRequest())
{
    $lotgdJaxon->processRequest();

    saveuser();

    $lotgdJaxon->sendResponse();

    exit;
}
