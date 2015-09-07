<?php
// translator ready
// addnews ready
// mail ready
require_once("lib/dbwrapper.php");
require_once("lib/e_rand.php");
require_once("lib/substitute.php");

function select_deathmessage($forest=true,$extra=array(),$extrarep=array()) {
	global $session, $badguy;

	$where=($forest?"WHERE forest=1":"WHERE graveyard=1");

	$sql = "SELECT deathmessage,taunt FROM " . db_prefix("deathmessages") .
		" $where ORDER BY rand(".e_rand() . ") LIMIT 1";

	$result = db_query($sql);
	if ($result) {
		$row = db_fetch_assoc($result);
		$deathmessage = $row['deathmessage'];
		$taunt=$row['taunt'];
	} else {
		$taunt=1;
		$deathmessage = "`5\"`6{goodguyname}'s mother wears combat boots`5\", screams {badguyname}.";
	}

	$deathmessage = substitute($deathmessage,$extra,$extrarep);
	return array("deathmessage"=>$deathmessage,"taunt"=>$taunt);
}

function select_deathmessage_array($forest=true,$extra=array(),$extrarep=array()){
	global $session, $badguy;
	
	$where=($forest?"WHERE forest=1":"WHERE graveyard=1");

	$sql = "SELECT deathmessage,taunt FROM " . db_prefix("deathmessages") .
		" $where ORDER BY rand(".e_rand() . ") LIMIT 1";

	$result = db_query($sql);
	if ($result) {
		$row = db_fetch_assoc($result);
		$deathmessage = $row['deathmessage'];
		$taunt=$row['taunt'];
	} else {
		$taunt=1;
		$deathmessage = "`5\"`6{goodguyname}'s mother wears combat boots`5\", screams {badguyname}.";
	}
	if ($extra[0]=='{where}') $deathmessage=str_replace($extra[0],$extrarep[0],$deathmessage);
	$deathmessage = substitute_array($deathmessage,$extra,$extrarep);
	array_unshift($deathmessage, true, "deathmessages");
	return array("deathmessage"=>$deathmessage,"taunt"=>$taunt);
}
?>
