<?php
if (get_module_pref("weapon")) {
	output("`2Your weapon has become more powerful!`n");
	$session['user']['weapondmg']++;
	$session['user']['attack']++;
}
if (get_module_pref("armor")){
	output("`2Your armor has become more powerful!`n");
	$session['user']['armordef']++;
	$session['user']['defense']++;
}			
?>