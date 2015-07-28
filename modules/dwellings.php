<?php

function dwellings_getmoduleinfo() {
	$info = array(		"name"=>"Dwellings Core",		"version"=>"20071026",		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=162",		"vertxtloc"=>"http://www.legendofsix.com/",		"author"=>"<a href='http://www.joshuadhall.com' target=_new>Sixf00t4</a>, Christian Rutsch, Chris Vorndran, `4Talisman`0, Oliver Brendel",		"category"=>"Dwellings",		"requires"=>array(		   "cityprefs"=>"20051110|By Sixf00t4, available on DragonPrime",		), 		"settings"=>array(			"Dwellings Settings,title",				"listnum" => "How many results per page on list?, int|50",				"namegemcost" => "How many gems does it cost to change the name?, int|2",				"namegoldcost" => "How much gold does it cost to change the name?, int|250",								"descgemcost" => "How many gems does it cost to change the in house description?, int|0",				"descgoldcost" => "How much gold does it cost to change the in house description?, int|0",				"windgemcost" => "How many gems does it cost to change the public description?, int|0",				"windgoldcost" => "How much gold does it cost to change the public description?, int|0",				"talkl" => "Allow users to change the talkline?, bool|1",				"villagenav" => "What menu entry should be used?, text|Local Dwellings",				"logoutlocation" => "What text should be used as location for people inside a dwelling?, text|Inside dwelling",				"ownergloballimit" => "How many dwellings are users allowed to own globally? (0 = infinite), int|1",				"commwhat"=>"Do what with the commentary when a dwelling is sold?,enum,0,Moderate,1,Delete|0",				"By selecting Moderate the comments will be placed for auditing and deleted from the commentary table.,note",			"Expired Account Settings,title",				"delete"=>"What should be done when the owner of a dwelling is deleted?, enum,						0,Delete the dwelling,						1,Set dwelling-status to 'abandoned',						2,Give the house to the first key owner,						3,Give the wife/husband - requires marriage|0",				"delete2"=>"What should be done if it can't do the above option., enum,					0,Delete the dwelling,					1,Set dwelling-status to abandoned|0",			"Coffer Settings,title",				"enablecof" => "Enable coffers?, bool|1",				"maxcofferwiths" => "Max user withdraws from a coffer per day?, int|3",				"maxcofferdeps" => "Max user deposited in a coffer per day?, int|3",				"maxcoffergold" => "Max GLOBAL gold in dwellings?, int|100000",				"maxcoffergems" => "Max GlOBAL gems in dwellings?, int|50",				"Set 0 on the GLOBAL values to set no limits.,note",			"Value Settings,title",				"abnperc" => "Percent of value the system will resell dwellings back for?, int|105",				"valueper" => "Percent of total cost the system will buy dwellings back for?, int|95",				"demoper" => "Percent already paid for that is refunded upon demolition?, int|45",				"Set 0 for none.,note",				"addcof" => "Add coffers to sell price?, bool|1",				"dumpcof" => "Dump coffers when a dwelling is sold?, bool|0",				"zerocof" => "Empty coffers when a dwelling is abandoned?, bool|0",				"lvlbuy" => "Allow players to buy previously owned dwellings when they don't meet the DK requirement for that type?, bool|0",				"levelsell" => "What level does a player need to be above to sell/demolish a dwelling?,int|10",				),		"prefs"=>array(			"Dwellings Preferences,title",				"location_saver" => "Which city was the person last in?, viewonly",				"dwelling_saver" => "Which dwelling ID was the player last in?, int|0",				"cofferwiths" => "How many times has this user withdrawn from a coffer today?, int|0",				"cofferdeps" => "How many times has this user deposited in a coffer today?, int|0",				),		"prefs-dwellings"=>array(			"Dwelling Object Prefs,title",			"buildturns"=>"How many turns have they spent on building this dwelling?,int|0",			"dwidtalkline"=>"What is the talk line for this dwelling?,text|says",			),			"prefs-city"=>array(			"allcitylimit" => "How many total dwellings are allowed here? (0 = infinite), int|0",			"ownercitylimit" => "How many total dwellings are users allowed to own here? (0 = infinite), int|1",			),		);
	return $info;
}

function dwellings_install(){
	global $session;
	require_once("modules/dwellings/install.php");
	return true;
}

function dwellings_uninstall(){
	output("`4Un-Installing dwellings Module.`n");
	$sql = "DROP TABLE IF EXISTS ".db_prefix("dwellings").", ".db_prefix("dwellingkeys").",".db_prefix("dwellingtypes")."";
	db_query($sql);
	$sql = "DELETE FROM ".db_prefix("module_objprefs")." WHERE objtype='dwellings'";
	db_query($sql);
	$sql = "DELETE FROM ".db_prefix("module_userprefs")." WHERE modulename='dwellings'";
	db_query($sql);
	$sql = "DELETE FROM ".db_prefix("commentary")." WHERE section LIKE 'dwellings-%' OR section LIKE 'coffers-%'";
	db_query($sql);
	return true;
}

function dwellings_dohook($hookname,$args){
	global $session;
	require("modules/dwellings/dohook/$hookname.php");
	return $args;
}

function dwellings_run() {
	checkday();
	page_header("Dwellings");
	global $session;
	$op = httpget("op");
	$dwid = httpget('dwid');
	$type = httpget('type');
	debug(get_module_pref("location_saver"));
	if($type == "" && $dwid>0){
		$sql = "SELECT type FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
		$result = db_query($sql);
		$row = db_fetch_assoc($result); 
		$type = $row['type'];
	}
	$cityid = httpget('cityid');	
	require_once("modules/dwellings/run/case_$op.php");
	if ($op != "list" && $op != ""){
		addnav("Leave");
		addnav("Return to Hamlet","runmodule.php?module=dwellings");
	}else{
		addnav("Navigation");
		villagenav();
	}
	page_footer();
}
?>