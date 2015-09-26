<?php
function dragoneggs_sanctum3(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Order of the Inner Sanctum");
	output("`n`c`b`&Order of the Inner Sanctum`b`c`7");
	output("Realizing that your time can be best spent by participating another `@2 turns`7 in the ritual, you gain another `%gem`7!");
	$session['user']['turns']-=2;
	$session['user']['gems']++;
	debuglog("received a gem and lost 2 turns in the same research turn by researching at the Order of the Inner Sanctum.");
	addnav("Return to the Order","runmodule.php?module=sanctum");
	villagenav();
}
?>