<?php
// translator ready
// addnews ready
// mail ready

function inv_statvalues_getmoduleinfo(){
	$info = array(
		"name"=>"Stat changing values for items",
		"version"=>"1.0",
		"author"=>"Christian Rutsch",
		"category"=>"Inventory",
		"download"=>"http://www.dragonprime.net/users/XChrisX/itemsystem.zip",
		"override_forced_nav"=>true,
		"requires"=>array(
			"inventory"=>"2.0|By Christian Rutsch, part of the itemsystem",
		),
		"settings"=>array(
		),
		"prefs-items"=>array(
			"Stat changes,title",
			"attack"=>"How much does this item increase the attack value?,int|0",
			"defense"=>"How much does this item increase the defense value?,int|0",
			"maxhitpoints" => "How many hitpoints are granted by this item?,int|0",
		)

	);
	return $info;
}
function inv_statvalues_install(){
	module_addhook("equip-item");
	module_addhook("unequip-item");
	module_addhook("dk-preserve");
	return true;
}

function inv_statvalues_uninstall(){
	return true;
}

function inv_statvalues_dohook($hookname,$args){
	global $session;
	switch ($hookname) {
		case "equip-item":
			$id = $args['id'];
			debug($id);
			$attack = get_module_objpref("items", $id, "attack");
			$defense = get_module_objpref("items", $id, "defense");
			$maxhitpoints = get_module_objpref("items", $id, "maxhitpoints");
			$session['user']['attack'] += $attack;
			$session['user']['defense'] += $defense;
			$session['user']['maxhitpoints'] += $maxhitpoints;
			if ($attack != 0 && $defense != 0 && $maxhitpoints != 0) {
				debuglog("'s stats changed due to equipping item $id: attack: $attack, defense: $defense, maxhitpoints: $maxhitpoints");
				debug("Your stats changed due to equipping item $id: attack: $attack, defense: $defense, maxhitpoints: $maxhitpoints");
			}
			break;
		case "unequip-item":
			while(list($key, $id) = each($args['ids'])) {
				$attack = -get_module_objpref("items", $id, "attack");
				$defense = -get_module_objpref("items", $id, "defense");
				$maxhitpoints = -get_module_objpref("items", $id, "maxhitpoints");
				$session['user']['attack'] += $attack;
				$session['user']['defense'] += $defense;
				$session['user']['maxhitpoints'] += $maxhitpoints;
				if ($attack != 0 && $defense != 0 && $maxhitpoints != 0) {
					debuglog("'s stats changed due to unequipping item $id: attack: $attack, defense: $defense, maxhitpoints: $maxhitpoints");
					debug("Your stats changed due to unequipping item $id: attack: $attack, defense: $defense, maxhitpoints: $maxhitpoints");
				}
			}
			break;
		case "dk-preserve":
			$sql = "SELECT itemid FROM inventory WHERE equipped = 1 AND userid = {$session['user']['acctid']}";
			$result = db_query($sql);
			while ($row = db_fetch_assoc($result)) {
				$id = $row['itemid'];
				$attack = -get_module_objpref("items", $id, "attack");
				$defense = -get_module_objpref("items", $id, "defense");
				$maxhitpoints = -get_module_objpref("items", $id, "maxhitpoints");
				$session['user']['attack'] += $attack;
				$session['user']['defense'] += $defense;
				$session['user']['maxhitpoints'] += $maxhitpoints;
				if ($attack != 0 && $defense != 0 && $maxhitpoints != 0)
					debuglog("'s stats changed due to equipping item $id: attack: $attack, defense: $defense, maxhitpoints: $maxhitpoints");
			}
	}
	return $args;
}

function inv_statvalues_run(){
}
?>