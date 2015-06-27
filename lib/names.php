<?php
// translator ready
// addnews ready
// mail ready
require_once("lib/dbwrapper.php");

function get_player_title($old=false) {
	global $session;
	$title = "";
	if ($old === false) {
		$title = $session['user']['title'];
		if ($session['user']['ctitle']) $title = $session['user']['ctitle'];
	} else {
		$title = $old['title'];
		if ($old['ctitle']) $title = $old['ctitle'];
	}
	return $title;
}

function get_player_basename($old=false) {
	global $session;
	$name = "";
	
	// legacy support below!
	$title = get_player_title($old);
	if ($old===false) {
		$name = $session['user']['name'];
		$pname = $session['user']['playername'];
	} else {
		$name = $old['name'];
		$pname = $old['playername'];
	}
	if ($pname!='') {
		return (str_replace("`0", "", $pname));
	}
	if ($title) {
		$x = strpos($name, $title);
		if ($x !== false)
			$pname = trim(substr($name,$x+strlen($title)));
	}

	$pname = str_replace("`0", "", $pname);
	
	return $pname;
}

function change_player_name($newname, $old=false) {
	if ($newname == "")
		$newname = get_player_basename($old);

	$newname = str_replace("`0", "", $newname);

	$title = get_player_title($old);

	//$session['user']['playername'] = str_replace("`0","",$newname);

	if ($title) {
		/*$x = strpos($newname, $title);
		if ($x === 0)
			$newname = trim(substr($newname, $x+strlen($title)));
		*/$newname =  $title . " " . $newname . "`0";
	}
	return $newname;
}

function change_player_ctitle($nctitle,$old=false) {
	global $session;
	if ($nctitle == "") {
		if ($old == false) {
			$nctitle = $session['user']['title'];
		} else {
			$nctitle = $old['title'];
		}
	}
	$newname = get_player_basename($old) . "`0";
	if ($nctitle) {
		$newname = $nctitle." ".$newname;
	}
	return $newname;
}

function change_player_title($ntitle, $old=false) {
	global $session;
	if ($old===false) {
		$ctitle = $session['user']['ctitle'];
	} else {
		$ctitle = $old['ctitle'];
	}

	$newname = get_player_basename($old) . "`0";
	if ($ctitle == "") {
		if ($ntitle != "") {
			$newname = $ntitle." ".$newname;
		}
	} else {
		$newname = $ctitle." ".$newname;
	}
	return $newname;
}

?>
