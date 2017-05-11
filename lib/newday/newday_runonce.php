<?php
//newday runonce

require_once 'lib/gamelog.php';

//Let's do a new day operation that will only fire off for
//one user on the whole server.
//run the hook.
modulehook('newday-runonce', []);

//Do some high-load-cleanup
if (datacache_clearExpired()) gamelog('Expired cache data has been deleted');
if (datacache_optimize()) gamelog('Cache data has been optimized', 'maintenance');

//only if not done by cron
if (! getsetting('newdaycron', 0))
{
	require_once 'lib/newday/dbcleanup.php';
	require 'lib/newday/commentcleanup.php';
	require 'lib/newday/charcleanup.php';
}
