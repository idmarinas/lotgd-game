<?php
function dragoneggs_diner27(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Hara's Bakery");
	output("`n`c`b`@Hara's `^Bakery`b`c`#`n");
	output("You quickly step in and break up the fight, suffering a significant cut on your arm and costing you a maximum hitpoint.`n`n");
	output("`%Hara`# thanks you and asks if you could help solve a little problem in the kitchen.  She leads you to the back room and you notice that there's a `&Dragon Egg`# close to hatching.");
	output("`n`nLuckily, it's very early in the hatching and it's quite easy to destroy.");
	output("`n`nYou successfully destroy the egg and `&gain a Dragon Egg Point`# and you also `%gain 2 gems`# for your efforts!");
	debuglog("spent a maxhitpoint to end a fight and gain a dragonegg point and 2 gems while researching dragon eggs at Hara's Bakery.");
	increment_module_pref("dragoneggs",1,"dragoneggpoints");
	increment_module_pref("dragoneggshof",1,"dragoneggpoints");
	$session['user']['gems']+=2;
	$session['user']['maxhitpoints']--;
	addnav("Return to Hara's Bakery","runmodule.php?module=bakery&op=food");
	villagenav();
}
?>