<?php

\define('ALLOW_ANONYMOUS', true);
\define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';

$controller = (string) \LotgdRequest::getQuery('controller', '');
$method     = (string) \LotgdRequest::getQuery('method', '');

//-- Only load if controller class exists
if ($controller && class_exists($controller))
{
    $ctrl = \LotgdKernel::get($controller);

    $allowAnonymous    = method_exists($ctrl, 'allowAnonymous') ? $ctrl->allowAnonymous() : false;
    //-- If not set default is true
    //-- The intended use of Stimulus is to load small blocks of HTML code.
    $overrideForcedNav = method_exists($ctrl, 'overrideForcedNav') ? $ctrl->overrideForcedNav() : true;
    do_forced_nav($allowAnonymous, $overrideForcedNav);

    \LotgdResponse::callController($controller, $method ?: 'index', true);

    /**
     * No need LotgdResponse::pageStart(); and LotgdResponse::pageEnd();
     *
     * Because Stimulus only load small blocks of HTML code.
     */
}
