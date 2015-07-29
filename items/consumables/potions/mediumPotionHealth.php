<?php
//Cura un 30% de la salud máxima
$healpoints = $session['user']['maxhitpoints'] * 0.3;
$out[] = restore_hitpoints($healpoints);