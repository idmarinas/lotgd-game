<?php

global $DB_DATACACHEPATH, $DB_USEDATACACHE, $gz_handler_on;

$adapter = DB::getAdapter();
$platform = $adapter->getPlatform();
$useful_vals = [
	"datacachepath"=>$DB_DATACACHEPATH,
	"usedatacache"=>(int)$DB_USEDATACACHE,
	"gziphandler"=>$gz_handler_on,
	"databasetype"=>$platform->getName(),
];

require_once 'lib/settings.class.php';

$settings = new settings('settings');

$vals = array_merge($settings->getArray(), $useful_vals);

output_notl($lotgd_tpl->renderLotgdTemplate('configuration/cache.twig', []), true);
output_notl('`n`n');
output('`^Legend`0:`n');
output('(D) This has been moved to the dbconnect.php`n');
output('(S) This is in settings.php`n`n');
rawoutput("<form action='configuration.php?settings=cache&op=save' method='POST'>");
addnav("","configuration.php?settings=cache&op=save");
lotgd_showform($setup_cache, $vals, true);
rawoutput("</form>");
