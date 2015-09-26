<?php
function dragoneggs_police23(){
	global $session;
	page_header(array("The Jail of %s", $session['user']['location']));
	output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
	if (is_module_active("jail")) $jail="jail";
	else $jail="djail";
	output("You decide to make the trade and give your %s`2 and accept the `@Lawful Sword`2; a weapon that's 2 points better than your old one!",$session['user']['weapon']);
	$session['user']['weapon']="`@Lawful Sword`0";
	$session['user']['weapondmg']+=2;
	$session['user']['attack']+=2;
	$session['user']['gems']-=3;
	debuglog("received a weapon upgrade for 3 gems to a Lawful Sword with an attack 2 higher by researching at the Jail.");
	addnav("Return to the Jail","runmodule.php?module=$jail");
	villagenav();
}
?>