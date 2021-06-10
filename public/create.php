<?php

// translator ready
// addnews ready
// mail ready

\define('ALLOW_ANONYMOUS', true);

require_once 'common.php';
require_once 'lib/serverfunctions.class.php';

\LotgdTool::checkBan();

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

$refer = (string) $request->query->get('r');

$params['refer'] = '';

if ($refer)
{
    $params['refer'] = '&r='.\htmlentities($refer, ENT_COMPAT, getsetting('charset', 'UTF-8'));
}

$textDomain = 'page_create'; //-- Namespace, textDomain for page

$trash  = (int) getsetting('expiretrashacct', 1);
$new    = (int) getsetting('expirenewacct', 10);
$old    = (int) getsetting('expireoldacct', 45);
$params = [
    'textDomain'        => $textDomain,
    'allowCreation'     => (bool) getsetting('allowcreation', 1),
    'serverFull'        => ServerFunctions::isTheServerFull(),
    'requireEmail'      => (int) getsetting('requireemail', 0),
    'requireValidEmail' => (int) getsetting('requirevalidemail', 0),
    'acctTrash'         => $trash,
    'acctNew'           => $new,
    'acctOld'           => $old,
];

$op = (string) $request->query->get('op');

//-- Init page
\LotgdResponse::pageStart('title.create', [], $textDomain);

if ('val' == $op || 'forgotval' == $op)
{
    \LotgdResponse::pageTitle('title.validate', [], $textDomain);
}

\LotgdNavigation::addHeader('common.category.login');
\LotgdNavigation::addNav('common.nav.login', 'index.php');

//-- Check server are in maintenance
if (getsetting('fullmaintenance', 0) || getsetting('maintenance', 0))
{
    $params['maintenanceMode'] = true;
}

$request->attributes->set('params', $params);

if ('forgotval' == $op)
{
    \LotgdResponse::callController(\Lotgd\Core\Controller\CreateController::class, 'forgotVal');

    //-- Finalize page
    \LotgdResponse::pageEnd();
}
elseif ('val' == $op)
{
    \LotgdResponse::callController(\Lotgd\Core\Controller\CreateController::class, 'val');

    //-- Finalize page
    \LotgdResponse::pageEnd();
}
elseif ('forgot' == $op)
{
    \LotgdResponse::callController(\Lotgd\Core\Controller\CreateController::class, 'forgot');

    //-- Finalize page
    \LotgdResponse::pageEnd();
}

\LotgdResponse::callController(\Lotgd\Core\Controller\CreateController::class, 'index');

//-- Finalize page
\LotgdResponse::pageEnd();
