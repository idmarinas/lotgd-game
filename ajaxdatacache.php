<?php

global $session, $fiveminuteload;

define('ALLOW_ANONYMOUS', true);
define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';

if (! $session['user']['loggedin']) return;


$op = httpget('op');

if ('optimize' == $op)
{
    datacache_optimize();

    echo 'ok';
}
elseif('clearexpire' == $op)
{
    datacache_clearExpired();

    echo 'ok';
}
elseif('clearall' == $op)
{
    datacache_empty();

    echo 'ok';
}
elseif('clearbyprefix' == $op)
{
    $prefix = httpget('prefix');

    massinvalidate($prefix, true);

    echo 'ok';
}
else
{
    echo 'error';
}
