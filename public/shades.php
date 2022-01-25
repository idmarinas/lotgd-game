<?php

use Lotgd\Core\Controller\ShadesController;

require_once 'common.php';

LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

//-- You can only stay in the shades if you're dead.
if ($session['user']['alive'])
{
    redirect('village.php');
}

//-- Init page
LotgdResponse::pageStart();

//-- Call controller
LotgdResponse::callController(ShadesController::class);

//-- Finalize page
LotgdResponse::pageEnd();
