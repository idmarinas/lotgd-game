<?php

// translator ready
// addnews ready
// mail ready

define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

$textDomain = 'page_referral';

if (! $session['user']['loggedin'])
{
    $referral = (string) \LotgdRequest::getQuery('r');

    \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.referral.create', [ 'referral' => $referral ], $textDomain));

    redirect('create.php?r='.rawurlencode($referral));
}

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain
];

if (file_exists('public/lodge.php'))
{
    \LotgdNavigation::addNav('common.nav.lodge', 'lodge.php');
}
else
{
    \LotgdNavigation::villageNav();
}

$request->attributes->set('params', $params);

\LotgdResponse::callController(Lotgd\Core\Controller\ReferralController::class);

//-- Finalize page
\LotgdResponse::pageEnd();
