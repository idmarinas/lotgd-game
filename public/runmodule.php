<?php

use Lotgd\Core\Controller\LotgdControllerInterface;

// translator ready
// addnews ready
// mail ready

\define('ALLOW_ANONYMOUS', true);
\define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';

$module     = (string) LotgdRequest::getQuery('module');
$admin      = (bool) LotgdRequest::getQuery('admin');
$controller = (string) LotgdRequest::getQuery('controller', '');
$method     = (string) LotgdRequest::getQuery('method', '');

//-- For migrating modules to bundles
if ( ! $module && $controller && class_exists($controller))
{
    $ctrl = LotgdKernel::get($controller);

    $allowAnonymous    = false;
    $overrideForcedNav = false;

    if ($ctrl instanceof LotgdControllerInterface)
    {
        $allowAnonymous    = $ctrl->allowAnonymous();
        $overrideForcedNav = $ctrl->overrideForcedNav();
    }

    do_forced_nav($allowAnonymous, $overrideForcedNav);

    LotgdResponse::pageStart();
    LotgdResponse::callController($controller, $method ?: 'index');
    LotgdResponse::pageEnd();
}
elseif (injectmodule($module, $admin))
{
    $info = get_module_info($module);

    $allowanonymous      = (bool) ($info['allowanonymous'] ?? false);
    $override_forced_nav = (bool) ($info['override_forced_nav'] ?? false);

    do_forced_nav($allowanonymous, $override_forced_nav);

    $starttime = microtime(true);
    $fname     = $mostrecentmodule.'_run';

    $fname();
    $endtime = microtime(true);
    $time    = $endtime - $starttime;

    if ($time >= 1.00 && ($session['user']['superuser'] & SU_DEBUG_OUTPUT))
    {
        LotgdResponse::pageDebug('Slow Module ('.round($time, 2)."s): {$mostrecentmodule}`n");
    }
}
else
{
    do_forced_nav(false, false);

    LotgdFlashMessages::addWarningMessage(LotgdTranslator::t('redirect.module.unactive', [], 'app_default'));

    if ($session['user']['loggedin'])
    {
        redirect('village.php');
    }

    redirect('index.php');
}
