<?php
function dragoneggs_historical13(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Hall of Fame");
	output("`c`b`^The Hall of Fame`b`c`@`n");
	if ($op2=="no"){
		output("You don't have time for Bird Watching! How crazy is that??");
		addnav("Return to Hall of Fame","hof.php");
		villagenav();
	}else{
		output("Well, this sounds like fun! You make some new friends.  Your `&charm increases by 2`@!");
		addnav(array("Search %s Square",getsetting("villagename", LOCATION_FIELDS)),"runmodule.php?module=dragoneggs&op=town");
		if (get_module_pref("researches")>=2) increment_module_pref("researches",-2);
		else increment_module_pref("researches",-1);
		$session['user']['charm']+=2;
		debuglog("gained 1-2 research turns and 2 charm by leaving to bird watch at the Capital Town Square while researching dragon eggs at the Hall of Fame.");
	}
}
?>