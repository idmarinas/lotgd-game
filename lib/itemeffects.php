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
		if ($session['user']['dragonkills'] >= $item['dragonkills'] && $session['user']['level'] >= $item['level'])
		{
			include_once($item['execvalue'].'.php');
		}
		else
		{
			$args = modulehook("item-noeffect", array("msg"=>"`&Nothing happens.`n", "item"=>$item));
			$out[] = sprintf_translate($args['msg']);
			$out[] = sprintf_translate('`4No tienes suficiente Rango y/o Nivel para poder usar `b%s`b`0.`n', $item['name']);
			$out[] = sprintf_translate('`4Para poder usar `b%s`b, necesitas Rango `i%s`i`0 y Nivel `i%s`i`0.`n', 
				$item['name'], 
				$item['dragonkills'],
				$item['level']
			);
		}
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
