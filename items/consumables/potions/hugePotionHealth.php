<?php
//Cura un 7% de la salud máxima
$healpoints = $session['user']['maxhitpoints'] * 0.7;
$out[] = restore_hitpoints($healpoints);