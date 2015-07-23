<?php
//this is for items that grant favor with Ramius.
//User resurrections are checked against a module pref, if user is found to have a higher number
//it's assumed they've been resurrected, and the favor the item(s) grants is restored. Neat, huh?
if (get_module_pref("favor") AND $session['user']['resurrections']>get_module_pref("res")){
	$favor = get_module_pref("favoradd");
	$session['user']['deathpower']+=$favor;
	output( '`2Thanks to enchantments on items you carry, `^%s favor`2 has been restored with %s`2.`n', $favor, getsetting( 'deathoverlord', '`$Ramius' ) );
	set_module_pref("res", $session['user']['resurrections'] + 1);
}
//if the item(s) grants extra turns, restore them upon newday
if (get_module_pref("turnadd")>0){
	$turns = get_module_pref("turnadd");
	$session['user']['turns']+=$turns;
	output("`2Thanks to enchantments on the items you carry, you gain `^%s `2extra turns.`n", $turns);
}
?>