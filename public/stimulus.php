<?php

\define('ALLOW_ANONYMOUS', true);
\define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';

$controller = (string) \LotgdRequest::getQuery('controller', '');
$method     = (string) \LotgdRequest::getQuery('method', '');

//-- For migrating modules to bundles (Temporal)
if ($controller && class_exists($controller))
{
    $ctrl = \LotgdKernel::get($controller);

    $allowAnonymous    = method_exists($ctrl, 'allowAnonymous') ? $ctrl->allowAnonymous() : false;
    $overrideForcedNav = method_exists($ctrl, 'overrideForcedNav') ? $ctrl->overrideForcedNav() : false;
    do_forced_nav($allowAnonymous, $overrideForcedNav);

    \LotgdResponse::pageStart();
    \LotgdResponse::callController($controller, $method ?: 'index', true);
}
else
{
    do_forced_nav(false, false);

    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('redirect.module.unactive', [], 'app_default'));

    if ($session['user']['loggedin'])
    {
        redirect('village.php');
    }

    redirect('index.php');
}
