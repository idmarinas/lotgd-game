<?php
//db cleanup
savesetting("lastdboptimize",date("Y-m-d H:i:s"));
$result = DB::query("SHOW TABLES");
$tables = array();
$start = getmicrotime();
for ($i=0;$i<DB::num_rows($result);$i++){
	list($key,$val)=each(DB::fetch_assoc($result));
	DB::query("OPTIMIZE TABLE $val");
	array_push($tables,$val);
}
$time = round(getmicrotime() - $start,2);
require_once("lib/gamelog.php");
gamelog("Optimized tables: ".join(", ",$tables)." in $time seconds.","maintenance");
?>
