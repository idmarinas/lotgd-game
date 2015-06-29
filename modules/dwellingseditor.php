<?php

function dwellingseditor_getmoduleinfo(){
	$info = array(
		"name"=>"Dwelling Editor",
		"version"=>"20060109",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=162",
		"vertxtloc"=>"http://dragonprime.net/users/sixf00t4/",
        "author"=>"<a href='http://www.joshuadhall.com' target=_new>Sixf00t4</a>",
		"category"=>"Dwellings",
        "requires"=>array(
	       "dwellings"=>"20060105|By Sixf00t4, available on DragonPrime",
        ), 
	);
	return $info;
}

function dwellingseditor_install() {
	module_addhook("superuser");
	module_addhook("moderate");
	return true;
}

function dwellingseditor_uninstall() {
	return true;
}

function dwellingseditor_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {
		case "superuser":
			if ($session['user']['superuser'] & SU_EDIT_USERS) {
				addnav("Editors");
				addnav("Dwelling Editor","runmodule.php?module=dwellingseditor");
			}
			break;
		case "moderate":
			$dwid = httpget('dwid');
			$area = httpget('area');
			if($area == "dwellings-$dwid"){
				addnav("Return");
				addnav("To Dwelling Editor","runmodule.php?module=dwellingseditor&op=dwsu&dwid=$dwid");
			}
			break;
	}
	return $args;
}

function dwellingseditor_run(){
	global $session;
	tlschema("dwellingseditor");
	$op = httpget('op');
	if($op != "lookup") page_header("Dwellings Editor");
	addnav("Navigation");  
	addnav("Back to the Grotto","superuser.php");  
	if($op != "") addnav("Dwelling List","runmodule.php?module=dwellingseditor");
	addnav("Find Dwellings by User","runmodule.php?module=dwellingseditor&op=usersearch");
	addnav("Operations");
	if($op != "typsu") addnav("Type Pref Editor","runmodule.php?module=dwellingseditor&op=typesu");  
	modulehook("dwellingseditor-main");
	$typeid = httpget("typeid");
	$dwid = httpget('dwid');
	$type = httpget("type");
	if($type == "" && $dwid > 0){
		$sql = "SELECT type FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
		$result = db_query($sql);
		$row = db_fetch_assoc($result); 
		$type = $row['type'];
	}	
	if($dwid > 0){
		addnav("Operations");
		if($op != "edit") addnav("Edit Dwelling Details","runmodule.php?module=dwellingseditor&op=edit&dwid=$dwid");
		if($op != "keys") addnav("Manage Keys","runmodule.php?module=dwellingseditor&op=keys&dwid=$dwid");
		if ($session['user']['superuser'] & SU_EDIT_COMMENTS) 
			addnav("Moderate Commentary","moderate.php?area=dwellings-$dwid&dwid=$dwid");
		if($op != "delete") addnav("Delete this Dwelling","runmodule.php?module=dwellingseditor&op=delete&dwid=$dwid");
        modulehook("dwellingseditor",array("dwid"=>$dwid));
		addnav("Navigation");
		if($op != "coffers") addnav("Coffer Log","runmodule.php?module=dwellingseditor&op=coffers&dwid=$dwid");
		if($op != "dwsu") addnav("Dwelling  Viewer","runmodule.php?module=dwellingseditor&op=dwsu&dwid=$dwid");
	}
	
	require_once("modules/dwellingseditor/case_$op.php");
	page_footer();
}
?>