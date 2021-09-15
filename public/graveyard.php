<?php

// addnews ready.
// translator ready
// mail ready

require_once 'common.php';
require_once 'lib/events.php';

/** @var Lotgd\Core\Http\Request $request */
$request = \LotgdKernel::get('Lotgd\Core\Http\Request');

//-- Init page
\LotgdResponse::pageStart();

$skipgraveyardtext = handle_event('graveyard');

$params = [
    'showGraveyardDesc' => ! $skipgraveyardtext,
];

if ( ! $skipgraveyardtext)
{
    if ($session['user']['alive'])
    {
        redirect('village.php');
    }

    //-- Check new day
    \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();
}

$request->attributes->set('params', $params);

//-- Call controller
\LotgdResponse::callController('Lotgd\Core\Controller\GraveyardController');

$params = $request->attributes->get('params');

if ('default' == $params['tpl'])
{
    module_display_events('graveyard', 'graveyard.php');
}

//-- Finalize page
\LotgdResponse::pageEnd();
