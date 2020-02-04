<?php

require_once 'lib/gamelog.php';

/**
 * This optimization not is necesary in InnoDB table
 * CronJob are deactivate by default
 */

//db cleanup
savesetting('lastdboptimize', date('Y-m-d H:i:s'));

$result = DB::query('SHOW TABLES');

$tables = [];
$start = microtime(true);

foreach ($result as $key => $value)
{
    foreach($value as $clave => $valor)
    {
        DB::query("OPTIMIZE TABLE $valor");
        array_push($tables, $valor);
    }
}

$time = round(microtime(true) - $start, 2);

gamelog('Optimized tables: '.join(', ', $tables)." in $time seconds.", 'maintenance');
