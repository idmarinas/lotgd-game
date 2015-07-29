<?php
//Cura un 10% de la salud máxima
$healpoints = $session['user']['maxhitpoints'] * 0.1;
$out[] = restore_hitpoints($healpoints);