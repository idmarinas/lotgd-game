<?php
function dragoneggs_diner21(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Hara's Bakery");
	output("`n`c`b`@Hara's `^Bakery`b`c`#`n");
	output("You get to work and make the place shine like it's never shined before. `%Hara`# is very happy with your work and she pays you `^%s gold`#.",$op2);
	debuglog("spent a turn to gain $op2 gold while researching dragon eggs at Hara's Bakery.");
	$session['user']['gold']+=$op2;
	$session['user']['turns']--;
	addnav("Return to Hara's Bakery","runmodule.php?module=bakery&op=food");
	villagenav();
}
?>