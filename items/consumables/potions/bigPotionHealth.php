<?php
//Cura un 50% de la salud máxima
$healpoints = $session['user']['maxhitpoints'] * 0.5;
$out[] = restore_hitpoints($healpoints);