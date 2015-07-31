<?php
// Itemeffect por Iván D.M 2015
// Lo que se puede modificar
// Curar salud actual -> restore_hitpoints($hitpoints, $overrideMaxhitpoints = false, $canDie = true)

/*
 * Curar la salud actual
 * @var int $hitpoins Puede ser negativo
 * @var bool $overrideMaxhitpoints Para permitir curar más de la salud máxima del personaje
 * @var bool $canDie Indica si puede morir por el efecto del artículo
 *
 * return $out array
 */
function restore_hitpoints($hitpoints, $overrideMaxhitpoints = false, $canDie = true)
{
	global $session;
	
	$hitpoints = (int) $hitpoints;
	
	//Se comprueba cuanto puede currarse
	$maxRestoreHP = $session['user']['maxhitpoints'] - $session['user']['hitpoints'];
	
	//Tiene salud para recuperar
	if (0 < $maxRestoreHP)
	{
		//## Calcular cuantos puntos se van a recuperar
		if (!$overrideMaxhitpoints)//No se permite superar el máximo de salud
		{
			$hitpoints = ($hitpoints > $maxRestoreHP) ? $maxRestoreHP : $hitpoints;
		}
		// Mayor que 0
		if (0 < $hitpoints)
		{
			if ($hitpoints == $maxRestoreHP)
			{
				$session['user']['hitpoints'] += $hitpoints;
				$out = sprintf_translate('Tu salud se ha restaurado `@completamente.`0`n');
			}
			else
			{
				$session['user']['hitpoints'] += $hitpoints;
				$out = sprintf_translate('Tu salud se ha `@restaurado`0 en `b%s`b puntos.`n', $hitpoints);
			}
			debuglog("Se ha curado $hitpoints puntos de salud usando el item {$item['itemid']}");
		}
		//Menor que cero baja la salud
		elseif (0 > $hitpoints)
		{
			//El usuario muere
			if (abs($hitpoints) < $session['user']['hitpoints'])
			{
				$session['user']['hitpoints'] += $hitpoints;
				$out = sprintf_translate('Estuviste `$a punto`0 de morir.`n');
				debuglog("Estuvo a punto de morir usando el item {$item['itemid']}");
			}
			//El usuario no puede morir
			else if (abs($hitpoints) >= $session['user']['hitpoints'] && false == $canDie)
			{
				$session['user']['hitpoints'] = 1;
				$out = sprintf_translate('`4Perdiste`0 `b%s`b puntos de salud.`n');
				debuglog("Perdió $hitpoints usando el item {$item['itemid']}");
			}
			else
			{
				$session['user']['hitpoints'] = 0;
				$session['user']['alive'] = 0;
				$out = sprintf_translate('`$Has muerto. Que pena.`n');
				debuglog("Murio cuando uso el item {$item['itemid']}");
			}
		}
		//Es cero
		else
		{
			$out = sprintf_translate('Usaste "`i%s`i" pero no tuvo ningún efecto.`n',$item['name']);
		}
	}	
	return $out;
}
		
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
		eval($item['execvalue']);
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
