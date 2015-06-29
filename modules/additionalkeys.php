<?php
function additionalkeys_getmoduleinfo(){
	$info = array(
		"name"=>"Additional Keys",
		"version"=>"1.1",
		"download"=>"http://www.pandea-island.de/downloads/additionalkeys.zip",
		"vertxtloc"=>"http://dragonprime.net/users/sixf00t4/",
        "author"=>"<a href='http://www.joshuadhall.com'>Sixf00t4</a> modified by Duena",
		"category"=>"Dwellings",
        "requires"=>array(
			"dwellings"=>"20051118|By Sixf00t4, available on DragonPrime",
        ), 
		"prefs-dwellings"=>array(
			"Dwelling Object Prefs,title",
			"addkeys"=>"How many additional keys does this dwelling have?,int|0",
		),	
		"prefs-dwellingtypes"=>array(
			"Dwellings Lodge Extrakeys Settings,title",			
				"keys"=>"Does this kind of building sell extra keys?,bool|0",
				"keygemcost"  => "How many gems does it cost to get an extra key?, int|0",
				"keygoldcost" => "How much gold does it cost to get an extra key?, int|0",
				"keydonationcost" => "How many donationpoints does it cost to get an extra key?, int|50",
        ), 
    );
	return $info;
}

function additionalkeys_install() {
	module_addhook("dwellings-maxkeys");
	module_addhook("dwellings-manage");
	return true;
}

function additionalkeys_uninstall() {
	return true;
}

function additionalkeys_dohook($hookname,$args) {
	$add = get_module_objpref("dwellings",$args['dwid'],"addkeys");
	$args['maxkeys']+=$add;
	
	global $session;
	switch($hookname){
		case "dwellings-manage":
			$type = $args['type'];
			$dwid = httpget('dwid');
			$typeid = get_module_setting("typeid",$type);
			addnav("Management");
			if (get_module_objpref("dwellingtypes",$typeid,"keys","additionalkeys") !=0)  
				addnav("Keymaker","runmodule.php?module=additionalkeys&op=buykeys&dwid=$dwid&type=$type");
			else 
				blocknav("Keymaker","runmodule.php?module=additionalkeys&op=buykeys&dwid=$dwid&type=$type");
			break;
		}
	return $args;	
}	

function additionalkeys_run(){
	global $session;

	$dwid = httpget('dwid');
	$type = httpget('type');
	$typeid = get_module_setting("typeid",$type);
	$op = httpget('op');
	$points = get_module_objpref("dwellingtypes", $typeid, "keydonationcost", "additionalkeys");
	$gem = get_module_objpref("dwellingtypes", $typeid, "keygemcost", "additionalkeys");
	$gold = get_module_objpref("dwellingtypes", $typeid, "keygoldcost", "additionalkeys");
	$pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];

	addnav("Navigation");
	addnav("Return to Management","runmodule.php?module=dwellings&op=manage&dwid=$dwid");

	page_header("Keymaker");
	
	switch ($op){
		case "buykeys":
			$type = httpget('type');
			$typeid = get_module_setting("typeid",$type);
			output("`n`0You enter the smithy and wait at the desk until a dwarf appears. \"`2Soso, you would like to buy more keys?`0\" he asks you and you nod. ");
			output("Then he tells you that this will cost you `^%s gold, %s gems `0and `^%s donationpoints`0. \"`2Still interested?`0\"",$gold, $gem, $points);
			addnav("Keymaker");
			addnav("Yes", "runmodule.php?module=additionalkeys&op=yes&dwid=$dwid&type=$type");
			break;			
		case "yes":
			$type = httpget('type');
			$typeid = get_module_setting("typeid",$type);
			if ($session['user']['gold'] < $gold 
				|| $session['user']['gems'] < $gem 
				|| $pointsavailable < $points){	
				output("`n`n\"`2Did I not just tell you what it costs to get an extra key? So why do you waste my precious time?!`0\" the keymaker yells at you. ");
				output("Embarassed you just nod, what is apparently you can do best und leave the keymakers office.");
			}else{
				$add = get_module_objpref("dwellings",$dwid,"addkeys","additionalkeys");
				$add++;
				set_module_objpref("dwellings",$dwid,"addkeys",$add,"additionalkeys");
				$session['user']['donationspent'] += $points;
				$session['user']['gems']-=$gem;
				$session['user']['gold']-=$gold;
				output("`n`n`0It takes a moment as the dwarf finally wipes away the sweat and returns to the desk, where you are still waiting for your key. He hands it over to you and says, \"`2Think about it carefully, whom you like to give it.`0\"");
			}
			break;
		}	
	page_footer();
}		
?>