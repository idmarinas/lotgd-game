<?php
function dragoneggs_jewelry3(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Oliver, the Jeweler");
	output("`c`b`&Oliver's Jewelry`b`c`7`n");
	$mod=$op2*3;
	$chance=e_rand($mod,19);
	if ($chance==19){
		output("You offer `3Oliver`7 the gems and he accepts your offer.`n`nYou pick up the jewel and feel a power surge through you!");
		output("`n`nYou `\$gain 2 permanent hitpoints`7 and `^increment your specialty`7!");
		$session['user']['gems']-=$op2;
		$session['user']['maxhitpoints']+=2;
		require_once("lib/increment_specialty.php");
		increment_specialty("`5");
		debuglog("spent $op2 gems to gain 2 hitpoints and increment specialty by researching at Oliver's Jewelry.");
	}else output("`3Oliver`7 looks at your offering. `&'Err... No.  No thank you.  It's not for sale.'`7  You keep your `%gems`7.");
	addnav("Return to Oliver's Jewelry","runmodule.php?module=jeweler");
	villagenav();
}
?>