<?php

define('ALLOW_ANONYMOUS', true);

require_once 'public/common.php';
require_once 'lib/gamelog.php';

//Do some high-load-cleanup
if (datacache_clearExpired())
{
    gamelog('Expired cache data has been deleted', 'maintenance');
}

if (datacache_optimize())
{
    gamelog('Cache data has been optimized', 'maintenance');
}
