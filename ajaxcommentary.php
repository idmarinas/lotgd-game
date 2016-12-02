<?php

global $session,$fiveminuteload;

define("ALLOW_ANONYMOUS",true);
define("OVERRIDE_FORCED_NAV",true);

require_once "common.php";

$now = time();
$minute = round($now/60)*60;

$session['chatrequests'][$minute] += 1;
//echo("Chat requests: ".$session['chatrequests'][$minute]." this minute (minute number ".$minute.").<br />");
if ($session['chatrequests'][$minute] >= 50){
	echo("Please don't run multiple Global Banter windows, it puts a tremendous strain on the server.  I've logged you out.  You'll be able to log back in again in a few minutes - please clear your cookies.");
	$session['user']['loggedin'] = false;
	saveuser();
	exit();
}

// if ($fiveminuteload>=8){
	// echo ("Server load is too high for auto-update at the moment.  This will hopefully balance out in a few minutes.");
	// exit();
// }

$expiresin = strtotime($session['user']['laston']) + 600;
$section = $_REQUEST['section'];
if ($now > $expiresin || ($session['user']['chatloc'] != "global_banter" && $section != "global_banter" && $session['user']['chatloc'] != $section  && $session['user']['chatloc']."_aux" != $section)){
	echo "Chat disabled due to inactivity";
} else {
	require_once "lib/commentary.php";
	$message = $_REQUEST['message'];
	$limit = $_REQUEST['limit'];
	$talkline = $_REQUEST['talkline'];
	$returnlink = urlencode($_REQUEST['returnlink']);
	$showmodlink = $_REQUEST['showmodlink'];
	
	$commentary = preparecommentaryblock($section,$message,$limit,$talkline,$schema=false,$skipfooter=false,$customsql=false,$skiprecentupdate=false,$showmodlink,$returnlink);
	$commentary = appoencode("`n".$commentary."`n",true);
	echo($commentary);
}
saveuser();
exit();


?>