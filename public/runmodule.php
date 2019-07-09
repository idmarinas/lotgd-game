<?php

// translator ready
// addnews ready
// mail ready

define('ALLOW_ANONYMOUS', true);
define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';
require_once 'lib/dump_item.php';
require_once 'lib/modules.php';

$module =  (string) \LotgdHttp::getQuery('module');
$admin = (bool) \LotgdHttp::getQuery('admin');

if (injectmodule($module, $admin))
{
    $info = get_module_info($module);

    $allowanonymous = (bool) ($info['allowanonymous'] ?? false);
    $override_forced_nav = (bool) ($info['override_forced_nav'] ?? false);

    do_forced_nav($allowanonymous, $override_forced_nav);

    $starttime = microtime(true);
    $fname = $mostrecentmodule.'_run';

    tlschema("module-$mostrecentmodule");
    $fname();
    $endtime = microtime(true);
    $time = $endtime - $starttime;

    if ($time >= 1.00 && ($session['user']['superuser'] & SU_DEBUG_OUTPUT))
    {
        debug('Slow Module ('.round($time, 2)."s): $mostrecentmodule`n");
    }
    tlschema();
}
else
{
    do_forced_nav(false, false);

    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('redirect.module.unactive', [], 'app-default'));

    if ($session['user']['loggedin'])
    {
        return redirect('village.php');
    }

    return redirect('index.php');
}
