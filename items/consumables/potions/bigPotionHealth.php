<?php
//Cura un 70% de la salud máxima
$healpoints = $session['user']['maxhitpoints'] * 0.7;
$healpoints = e_rand($healpoints/2,$healpoints);
$result = restore_hitpoints($healpoints);

if ($result)
	$out[] = $result;