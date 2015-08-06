<?php
// Itemeffect por Iván D.M 2015
// Lo que se puede modificar
// Curar salud actual -> restore_hitpoints($hitpoints, $overrideMaxhitpoints = false, $canDie = true)
require_once('lib/itemeffects/health.php');
	
function get_effect($item = false, $noeffecttext = "", $giveoutput = true) {
	global $session;
	tlschema("inventory");
	$out = array();
	if ($item === false) {
		if ($noeffecttext == "") {
			$args = modulehook("item-noeffect", array("msg"=>"`&Nothing happens.`n", "item"=>$item));
			$out[] = sprintf_translate($args['msg']);
		} else {
			$out[] = sprintf_translate($noeffecttext);
		}
	} else {
		include_once($item['execvalue']);
	}
	foreach($out as $index=>$text) {
		if (is_array($text)) {
			foreach($text as $sec=>$text2) {
				$newout[] = $text2;
			}
		} else {
			$newout[] = $text;
		}
	}
	$args = modulehook("itemeffect", array("out"=>$newout, "item"=>$item));
	$out = $args['out'];
	if (count($out) == 0) {
		if ($noeffecttext == "") {
			$args = modulehook("item-noeffect", array("msg"=>"`&Nothing happens.`n", "item"=>$item));
			$out[] = sprintf_translate($args['msg']);
		} else if ($giveoutput) {
			$out[] = sprintf_translate($noeffecttext);
		}
	}

	$effect_text = join($out, "");
	tlschema();
	return $effect_text;
}
?>
