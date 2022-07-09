<?php

//newday runonce

//Let's do a new day operation that will only fire off for
//one user on the whole server.
//run the hook.

use Lotgd\Core\Event\Core;

\LotgdEventDispatcher::dispatch(new Core(), Core::NEWDAY_RUNONCE);

//only if not done by cron
if ( ! LotgdSetting::getSetting('newdaycron', 0))
{
    require 'lib/newday/commentcleanup.php';
    require 'lib/newday/charcleanup.php';
    require 'lib/newday/logoutaccts.php';
    require 'lib/newday/petition_cleanup.php';
}
