<?php

global $DB_DATACACHEPATH, $DB_USEDATACACHE, $gz_handler_on;

$setup_cache = include_once 'lib/data/configuration_cache.php';

$adapter = DB::getAdapter();
$platform = $adapter->getPlatform();
$vals = [
    'datacachepath' => $DB_DATACACHEPATH,
    'usedatacache' => (int) $DB_USEDATACACHE,
    'gziphandler' => $gz_handler_on,
    'databasetype' => $platform->getName(),
];

output_notl($lotgd_tpl->renderLotgdTemplate('configuration/cache.twig', []), true);
output_notl('`n`n');
output('`^Legend`0:`n');
output('(D) This has been moved to the dbconnect.php`n');
output('(S) This is in settings.php`n`n');
rawoutput("<form action='configuration.php?settings=cache&op=save' method='POST'>");
lotgd_showform($setup_cache, $vals, true);
rawoutput('</form>');
