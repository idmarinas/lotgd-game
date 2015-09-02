<?php
function uniqueforests_getmoduleinfo(){
	$info = array(
		"name"=>"Unique Forests",
		"version"=>"20070427",
		"author"=>"<a href='http://www.joshuadhall.com'>Sixf00t4</a> blatantly ripped from XChrisX with love",
		"category"=>"Cities",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1242",
		"description"=>"Allows renaming of forests",
		"prefs-city"=>array(
			"The unique forests - Settings,title",
			"use"=>"Use a unique forest here?,bool|0",
			"name"=>"What is this forest called?|The Desert",
			"desc"=>"What is the description?,textarea|`c`pThe sea of lost dreams`c`n`n`xAs far as your eyes can see, you are sorrounded by a sea of sand and dust that awaits those ready and brave enough to explore it.  
				Once in a while the warm wind picks up some grains of sand, whirling them around in the hot sun of the day, leading them to dance and sparkle through the air.  
				Just to be picked up again as they are close to touching the ground again.  
				The whole view is bathed in red and golden warmth which like waves invite you to lose yourself in the beauty of this amazing view and let yourself be drifted away.  
				But your eyes may be deceived. What lurks below the sand? What's hidden from the eyes of an unknowing, uncautious traveler?",
		),
		"requires"=>array(
		   "cityprefs"=>"20051110|By Sixf00t4, available on DragonPrime",
		), 
	);
	return $info;
}

function uniqueforests_install(){
	module_addhook_priority("village",20);
	module_addhook("header-forest");
	module_addhook("footer-forest");
	module_addhook("forest-desc");
	module_addhook("collect-events");
	return true;
}

function uniqueforests_uninstall(){
	return true;
}

function uniqueforests_dohook($hookname,$args){
	global $session;
	require_once("modules/cityprefs/lib.php");
	$cityid=get_cityprefs_cityid("location",$session['user']['location']);
	switch ($hookname){
		case "collect-events":
			if (get_module_objpref("city",$cityid,"use")) {
				foreach($args as $index => $event) {
					$event['rawchance'] = 0;
					$events[$index] = $event;
				}
				$args = $events;
			}
			break;
		case "village":
			if (get_module_objpref("city",$cityid,"use")) {
				addnav($args['gatenav']);
				$name=get_module_objpref("city",$cityid,"name");
				blocknav("forest.php");
				addnav(array("%s",$name), "forest.php?location=uniqueforests");
			}
			break;
		case "header-forest":
			if (httpget('location')=="uniqueforests" || get_module_objpref("city",$cityid,"use")) {
				if (httpget('op') == "") {
					global $block_new_output;
					$block_new_output = true;
				}
				blocknav("runmodule.php",true);
				blocknav("healer.php",true);
				tlschema("module-uniqueforests");
			}
			break;
		case "footer-forest":
			if (httpget('location')=="uniqueforests" || get_module_objpref("city",$cityid,"use")) {
				$name=get_module_objpref("city",$cityid,"name");
				page_header(color_sanitize($name));
			}
			break;
		case "forest-desc":
			if (httpget('location')=="uniqueforests" || get_module_objpref("city",$cityid,"use")) {
				page_header(color_sanitize($name));
				global $block_new_output;
				$block_new_output = false;
				output(get_module_objpref("city",$cityid,"desc"));
				$block_new_output = true;
			}
			break;
	}
	return $args;
}

function uniqueforests_run(){
}
?>