<?php

function creaturedrop_getmoduleinfo() {
	$info = array(
		"name"=>"Creature Item Drop",
		"author"=>"Iori, tips from XChrisX",
		"version"=>"1.2",
		"category"=>"Inventory",
		"download"=>"here",
		"requires"=>array(
			"inventory"=>"Inventory Basic System, by XChrisX",
		),
		"prefs-creatures"=>array(
			"Creature Drop,title",
			"drop"=>"Which item can this creature drop?,item|",
			"chance"=>"Percentage chance the item will be dropped upon victory?,range,0,100,1|0",
			"dk"=>"How many Dragonkills must the player have before this item can be dropped by this creature?,int|0",
		),
	);
	return $info;
}

function creaturedrop_install() {
	module_addhook("battle-victory");
	return true;
}

function creaturedrop_uninstall() {
	return true;
}

function creaturedrop_dohook($hookname,$args) {
	global $session;
	if ($hookname == "battle-victory") {
		$chance = get_module_objpref("creatures",$args['creatureid'],"chance");
		$dk = get_module_objpref("creatures",$args['creatureid'],"dk");
		if ($session['user']['dragonkills'] >= $dk && ($args['type'] == 'forest' || $args['type'] == 'travel') && e_rand(1,100) <= $chance) {
			$drop = get_module_objpref("creatures",$args['creatureid'],"drop");
			require_once("lib/itemhandler.php");
			if (add_item((int)$drop)) {
				$itemname = get_item_by_id($drop);
				output("`n`c`^Searching the lifeless body of `4%s`^ you find a `7%s`^.`c`n",$args['creaturename'],$itemname['name']);
			}
		}
	}
	return $args;
}
?>