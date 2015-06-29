<?php
require_once("lib/http.php");

function dwitems_getmoduleinfo(){
	$info = array(
		"name"=>"Itemssystem for Dwellings",
		"version"=>"1.1",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=download;id=1080;mirror=1",
		"author"=>"Maeher",
		"category"=>"Dwellings",
		"description"=>"Creates items for dwellings",
		"requires"=>array(
			"dwellings"=>"20060724|By Sixf00t4, available on DragonPrime",),
		"settings"=>array(
			"maxchance"=>"What is the highest possibility (in %) a user can achieve through multiple items?,range,0,100,5|50",
			"location"=>"Where does the shop appear,location|".getsetting("villagename", LOCATION_FIELDS),
			"mindk"=>"How many dragonkills are needed to see the shop?,int|0",
		),
	);
	return $info;
}

function dwitems_install(){
	global $session;
	require("modules/dwitems/install.php");
	return true;
}

function dwitems_uninstall() {
	require("modules/dwitems/uninstall.php");
	return true;
}

function dwitems_dohook($hookname,$args) {
	global $session;
	require("modules/dwitems/dohook/$hookname.php");
	return $args;
}

function dwitems_run(){
	global $session;
	$op = httpget('op');
	page_header("Maeher's Household Supply");
	require("modules/dwitems/run/$op.php");
	page_footer();
}
?>