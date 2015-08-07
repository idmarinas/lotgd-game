<?php
//Cura un 90% de la salud máxima
$healpoints = $session['user']['maxhitpoints'] * 0.9;
$healpoints = e_rand($healpoints/2,$healpoints);
$result = restore_hitpoints($healpoints);

if ($result)
	$out[] = $result;