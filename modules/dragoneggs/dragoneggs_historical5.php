<?php
function dragoneggs_historical5(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Hall of Fame");
	output("`c`b`^The Hall of Fame`b`c`@`n");
	if ($op2==""){
		$cost=min($session['user']['level']*50,500);
		$session['user']['gold']-=$cost;
		$level=$session['user']['level'];
		$chance=e_rand(1,7);
		output("You place the `^%s gold`@ on the counter and give a small `#'Ahem'`@.",$cost);
		if (($level>8 && $chance<=5) || ($level<=8 && $chance<=3)){
			output("He takes your money and ushers you into the back room where mysteries await!");
			addnav("Continue","runmodule.php?module=dragoneggs&op=historical5&op2=continue");
			debuglog("incremented specialty and gained a gem for $cost gold while researching dragon eggs at the Hall of Fame.");
			blocknav("village.php");
			blocknav("hof.php");
		}else{
			output("He slides the money into his pocket and looks at you with a blank stare.");
			output("`n`n`2'Can I help you?'`@ he asks.`n`n");
			output("You realize he isn't going to let you into the Private Reading Room this time.");
			debuglog("lost $cost gold failing to bribe an archivist while researching dragon eggs at the Hall of Fame.");
		}
	}else{
		$session['user']['gems']++;
		output("You start looking around and find a gem embeddded in the binding of a book.");
		output("`n`nYou `%gain a gem`@ and then discover something even better! You improve in your specialty!`n");
		require_once("lib/increment_specialty.php");
		increment_specialty("`&");
	}
	addnav("Return to Hall of Fame","hof.php");
	villagenav();
}
?>