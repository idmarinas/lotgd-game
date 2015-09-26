<?php
function dragoneggs_witch25(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Old House");
	output("`c`b`&The Old House`b`c`7`n");
	if ($op2=="close"){
		output("You use `%3 gems`7 to cast a spell to destroy the dragon egg.  Congratulations!!");
		$session['user']['gems']-=3;
		increment_module_pref("dragoneggs",1,"dragoneggpoints");
		increment_module_pref("dragoneggshof",1,"dragoneggpoints");
		addnews("`7Thanks to the work of %s`7, a dragon egg was destroyed, thereby saving many lives!",$session['user']['name']);
		debuglog("gained a dragonegg point by spending 3 gems to destroy an egg while researching dragon eggs at the Old House.");
	}else{
		output("You defeat the `QHeat Vampire`7 and find yourself standing next to the dragon egg.");
		output("You have a chance to destroy the egg if you cast a spell using `%3 gems`7. Will you do it?");
		addnav("Destroy the Dragon Egg","runmodule.php?module=dragoneggs&op=witch25&op2=close");
	}
	addnav("Return to The Old House","runmodule.php?module=oldhouse");
	villagenav();
}
?>