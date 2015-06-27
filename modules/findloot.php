<?php

function findloot_getmoduleinfo(){
	$info = array(
		"name"=>"Find Loot",
		"author"=>"Christian Rutsch",
		"version"=>"1.01",
		"category"=>"Inventory",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1033",
		"settings"=>array(
			"Find Loot - Settings,title",
			"wkchance"=>"Chance to find loot after a forest fight, range, 0, 100, 1|12",
			"tvchance"=>"Chance to find loot after a travel fight, range, 0, 100, 1|15",
		),
	);
	return $info;
}
function findloot_install(){
	module_addhook("battle-victory");
	debug("Please use the item-editor to create some items with the category 'Loot' in order to use this mod properly.");
	module_addhook("hw-preserve");
	return true;
	}
function findloot_uninstall(){
	return true;
}
function findloot_dohook($hookname,$args){
	global $session;
	require_once("lib/itemhandler.php");

	switch ($hookname){
		case "battle-victory":
			$chance = e_rand(1,100);
			if (($args['type'] == 'forest' && $chance<=get_module_setting("wkchance")) || ($args['type'] == 'travel' && $chance<=get_module_setting("tvchance"))) {
				$loot = get_random_item("Loot");
				// get_random_item() Will return false, when no matching item is found
				// and return a proper item-array, when one is found.
				if ($loot != false) {
					// Important: When handing over the itemid, make sure to hand it as real INT,
					// otherwise the function will search for an item named "1"
					if (add_item((int)$loot['itemid']))
						output("`^Searching the lifeless body of `4%s`^ you find a `7%s`^.`n", $args['creaturename'], $loot['name']);
				}
			}
			break;
		case "hw-preserve":
			$sql = "DELETE FROM ".db_prefix('inventory')." WHERE itemid IN (SELECT itemid FROM ".db_prefix('item')." WHERE class = 'Loot') AND userid = {$session['user']['acctid']}";
			db_query($sql);
			break;
	}
	return $args;
}

?>