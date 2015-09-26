<?php
function dragoneggs_historical17(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Hall of Fame");
	output("`c`b`^The Hall of Fame`b`c`@`n");
	if ($op2=="no"){
		output("You realize there's something suspicious about this fellow so you decline.");
		addnav("Return to Hall of Fame","hof.php");
		villagenav();
	}else{
		output("Well, this sounds like a good idea!");
		if ($op2=="yes1") addnav("Research Merick's Stables","runmodule.php?module=dragoneggs&op=animal");
		else addnav(array("Research %s Square",getsetting("villagename", LOCATION_FIELDS)),"runmodule.php?module=dragoneggs&op=town");
		if (get_module_pref("researches")>=2) increment_module_pref("researches",-2);
		else increment_module_pref("researches",-1);
		debuglog("gained 1-2 research turns by leaving to research at the hospital or Capital Town Square while researching dragon eggs at the Hall of Fame.");
	}
}
?>