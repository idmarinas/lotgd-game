<?php

define("OVERRIDE_FORCED_NAV",true);
require("common.php");

function mail_status($args=false) {
	if ($args===false) return;
	$timeout_setting=120; // seconds
	$new=maillink();
	$objResponse = new xajaxResponse();
	$objResponse->assign("maillink","innerHTML", $new);
/*	global $session;
	$warning='';
	$timeout=strtotime($session['user']['laston'])-strtotime(date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds")));
	if ($timeout<=1) {
		$warning="<br/>".appoencode("`\$`b")."Your session has timed out!".appoencode("`b");
	} elseif ($timeout<120){
		$warning="<br/>".appoencode("`t").sprintf("TIMEOUT in %s seconds!",$timeout);
	} else $warning='';
	$objResponse->assign("notify","innerHTML", $warning);*/
	return $objResponse;
}

function timeout_status($args=false) {
	if ($args===false) return;
	$timeout_setting=120; // seconds
	global $session;
	$warning='';
	$timeout=strtotime($session['user']['laston'])-strtotime(date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds")));
	if ($timeout<=1) {
		$warning="<br/>".appoencode("`\$`b")."Your session has timed out!".appoencode("`b");
	} elseif ($timeout<120){
		$warning="<br/>".appoencode("`t").sprintf("TIMEOUT in %s seconds!",$timeout);
	} else $warning='';
	$objResponse = new xajaxResponse();
	$objResponse->assign("notify","innerHTML", $warning);
	return $objResponse;
}


function commentary_text($args=false) {
	global $session;
	if ($args===false || !is_array($args)) return;
	$section=$args['section'];
	$message="";
	$limit=25;
	$talkline="says";
	$schema=$args['schema'];
	$viewonly=$args['viewonly'];	
	$new=viewcommentary($section, $message, $limit, $talkline, $schema,$viewonly,1);
	$new=maillink();
	$objResponse = new xajaxResponse();
	$objResponse->assign($section,"innerHTML", $new);
}



require("mailinfo_common.php");
$xajax->processRequest();





?>
