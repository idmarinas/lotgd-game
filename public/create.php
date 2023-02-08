<?php

use Lotgd\Core\Controller\CreateController;
use Lotgd\Core\Http\Request;

// translator ready
// addnews ready
// mail ready

\define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

LotgdTool::checkBan();

/** @var Lotgd\Core\Http\Request $request */
$request = LotgdKernel::get(Request::class);

$refer = (string) $request->query->get('r');

$params['refer'] = '';

if ('' !== $refer && '0' !== $refer)
{
    $params['refer'] = '&r='.htmlentities($refer, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8'));
}

$textDomain = 'page_create'; //-- Namespace, textDomain for page

$trash  = (int) LotgdSetting::getSetting('expiretrashacct', 1);
$new    = (int) LotgdSetting::getSetting('expirenewacct', 10);
$old    = (int) LotgdSetting::getSetting('expireoldacct', 45);
$params = [
    'textDomain'        => $textDomain,
    'allowCreation'     => (bool) LotgdSetting::getSetting('allowcreation', 1),
    'serverFull'        => LotgdKernel::get('lotgd_core.service.server_functions')->isTheServerFull(),
    'requireEmail'      => (int) LotgdSetting::getSetting('requireemail', 0),
    'requireValidEmail' => (int) LotgdSetting::getSetting('requirevalidemail', 0),
    'acctTrash'         => $trash,
    'acctNew'           => $new,
    'acctOld'           => $old,
];

$op = (string) $request->query->get('op');

//-- Init page
LotgdResponse::pageStart('title.create', [], $textDomain);

if ('val' == $op || 'forgotval' == $op)
{
    LotgdResponse::pageTitle('title.validate', [], $textDomain);
}

LotgdNavigation::addHeader('common.category.login');
LotgdNavigation::addNav('common.nav.login', 'index.php');

//-- Check server are in maintenance
if (LotgdSetting::getSetting('fullmaintenance', 0) || LotgdSetting::getSetting('maintenance', 0))
{
    $params['maintenanceMode'] = true;
}

$request->attributes->set('params', $params);

if ('forgotval' == $op)
{
    LotgdResponse::callController(CreateController::class, 'forgotVal');

    //-- Finalize page
    LotgdResponse::pageEnd();
}
elseif ('val' == $op)
{
    LotgdResponse::callController(CreateController::class, 'val');

    //-- Finalize page
    LotgdResponse::pageEnd();
}
elseif ('forgot' == $op)
{
    LotgdResponse::callController(CreateController::class, 'forgot');

    //-- Finalize page
    LotgdResponse::pageEnd();
}

LotgdResponse::callController(CreateController::class, 'index');

//-- Finalize page
LotgdResponse::pageEnd();
