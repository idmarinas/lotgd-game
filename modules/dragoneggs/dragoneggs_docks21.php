<?php
function dragoneggs_docks21(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("The Docks");
	output("`c`b`^The Docks`b`c`7`n");
	$session['user']['gold']-=$op2;
	$expbonus=$session['user']['dragonkills']*e_rand(2,5);
	$expgain =$session['user']['level']*e_rand(25,65)+$expbonus;
	$session['user']['experience']+=$expgain;
	if (is_module_active("oceanquest")) $docks="oceanquest";
	else $docks="docks";
	output("You hand him `^%s gold`7 and he leaves you standing there with nothing.",$op2);
	output("`n`nBefore you get a chance to complain, he returns.  He's wiping blood off a dagger.");
	output("`n`nHe hands you a slip of paper with quickly scribbled handwriting.  `3'Here's how I killed one of them creatures,'`7 he says.");
	output("`n`nYou read the paper and `#gain `^%s`# experience`7.",$expgain);
	debuglog("gained $expgain experience for $op2 gold while researching dragon eggs at the Docks.");
	addnav("Return to the Docks","runmodule.php?module=$docks&op=docks&op2=enter");
	addnav("Return to the Forest","forest.php");
}
?>