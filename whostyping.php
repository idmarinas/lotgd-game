<?php

global $session,$fiveminuteload;

define("ALLOW_ANONYMOUS",true);
define("OVERRIDE_FORCED_NAV",true);
define("NO_SAVE_USER",true);

//echo("Test!");

require_once "common.php";

$now = time();
$minute = round($now/60)*60;

if (! isset($session['typerequests'][$minute])) $session['typerequests'][$minute] = 0;
$session['typerequests'][$minute] += 1;
//echo("Type requests: ".$session['typerequests'][$minute]." this minute (minute number ".$minute.").<br />");
if ($session['typerequests'][$minute] >= 200){
	echo("Please don't run multiple Global Banter windows, it puts a tremendous strain on the server.  I've logged you out.  You'll be able to log back in again in a few minutes - please clear your cookies.");
	$session['user']['loggedin'] = false;
	saveuser();
	exit();
}

if ($fiveminuteload>=8){
	echo ("Server load is too high for auto-update at the moment.  This will hopefully balance out in a few minutes.");
	exit();
}


//$start = getmicrotime(true);

//echo("Test!");

// require_once("lib/dump_item.php");
// require_once("lib/output.php");
// $text = appoencode(dump_item($session));
// echo($text);
// echo("Test!");

$section = $_REQUEST['section'];
$updateplayer = $_REQUEST['updateplayer'];
$name = addslashes($session['user']['name']);
$now = time();

if (! isset($session['iterations'])) $session['iterations'] = 0;
$session['iterations'] += 1;

$old = $now - 2;

//update time
if ($updateplayer){
	$sql = "INSERT INTO ".DB::prefix("whostyping")." (time,name,section) VALUES ('$now','$name','$section') ON DUPLICATE KEY UPDATE time = VALUES(time), section = VALUES(section)";
	DB::query($sql);
	//echo("Updating player");
	//erase old entries once per ten seconds
	$lastdigit = substr($now,-1);
	if ($lastdigit=="0"){
		$delsql = "DELETE FROM ".DB::prefix("whostyping")." WHERE time < $old";
		DB::query($delsql);
	}
	invalidatedatacache("whostyping/whostyping_$section");
}

//retrieve, deleting as appropriate
$sql = "SELECT * FROM ".DB::prefix("whostyping")." WHERE section='$section'";
$result = DB::query_cached($sql,"whostyping/whostyping_".$section,60);
$disp = array();
while ($row = DB::fetch_assoc($result)){
	if ($row['time'] > $old){
		$disp[]=$row['name'];
	}
}

//DB::free_result($result);

//display
foreach($disp AS $name){
	$encodedname = appoencode($name."`0 takes a breath...`n");
	echo($encodedname);
}

unset($disp);

// $end = getmicrotime(true);
// $total = $end - $start;
//echo("CavemanJoe is debugging in the middle of the night, and this cycle of whostyping.php took this long: ");
//echo($total);
//echo("test!");
//echo($session['iterations']);

exit();

?>