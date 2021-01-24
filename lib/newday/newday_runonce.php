<?php

//newday runonce

//Let's do a new day operation that will only fire off for
//one user on the whole server.
//run the hook.
\LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CORE_NEWDAY_RUNONCE, null, $args);
modulehook('newday-runonce', []);

//only if not done by cron
if ( ! getsetting('newdaycron', 0))
{
    require_once 'lib/gamelog.php';

    require 'lib/newday/commentcleanup.php';
    require 'lib/newday/charcleanup.php';
    require 'lib/newday/logoutaccts.php';
    require 'lib/newday/petition_cleanup.php';
}
