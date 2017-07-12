<?php

require_once 'lib/gamelog.php';

//db cleanup
savesetting('lastdboptimize', date('Y-m-d H:i:s'));

$result = DB::query("SHOW TABLES");

$tables = [];
$start = microtime(true);

foreach($result as $key => $value)
{
    list($clave, $valor) = each($value);
    DB::query("OPTIMIZE TABLE $valor");
    array_push($tables, $valor);
}

$time = round(microtime(true) - $start,2);

gamelog("Optimized tables: ".join(", ",$tables)." in $time seconds.","maintenance");
