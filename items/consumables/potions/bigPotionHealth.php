<?php
//Cura un 50% de la salud máxima
$healpoints = $session['user']['maxhitpoints'] * 0.5;
$healpoints = e_rand($healpoints/2,$healpoints);
$result = restore_hitpoints($healpoints);

if ($result)
	$out[] = $result;