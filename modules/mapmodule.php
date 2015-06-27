<?php
define("ALLOW_ANONYMOUS",true);
require_once("lib/http.php");

function mapmodule_getmoduleinfo(){
	$info=array(
		"name"=>"Map",
		"version"=>"1.0",
		"author"=>"Matthew Crouse",
		"category"=>"General",
		"settings"=>array(
			"Map Settings,title",
			"pagetitle"=>"Page Title.,text|Layout of the Land",
			"linktext"=>"Link Display.,text|View the Land",
			"mapimage"=>"Path to map image.,text|Please Notify your Administrator, that they have yet to set the Image for this Module",
			),
	);
return $info;
}

function mapmodule_install(){
	module_addhook("village");
	module_addhook("index");
	return true;
}

function mapmodule_uninstall(){
	return true;
}

function mapmodule_dohook($hookname,$args){
	$title = get_module_setting("pagetitle");
	$link = get_module_setting("linktext");
	$map = get_module_setting("mapimage");
	switch($hookname){
	
	case "village":
		addnav($args["othernav"]);
		addnav($link,"runmodule.php?module=mapmodule&op=image");
	break;
	
	case "index":
		addnav($link,"runmodule.php?module=mapmodule&op=image");
	break;
	}
	return $args;
}

function mapmodule_run(){
	global $session;
	$title = get_module_setting("pagetitle");
	$link = get_module_setting("linktext");
	$map = get_module_setting("mapimage");
	page_header("$title");
	$op=httpget("op");
	if ($op == "image"){
		rawoutput("<img src=$map>");
	}
	if ($session['user']['loggedin']) {
		addnav("Return to the Village","village.php");
	}else{
		addnav("Login Page","index.php");
	}
	page_footer();
}

?>