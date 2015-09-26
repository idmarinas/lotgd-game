<?php
function dragoneggs_police5(){
	global $session;
	page_header(array("The Jail of %s", $session['user']['location']));
	output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
	if (is_module_active("jail")) $jail="jail";
	else $jail="djail";
	output("You look around and don't see anyone watching.  You quickly switch out the Deputy's Sword and leave your %s`2 behind.",$session['user']['weapon']);
	$session['user']['weapon']="`#Deputy's Sword`0";
	$session['user']['weapondmg']++;
	$session['user']['attack']++;
	debuglog("received a weapon upgrade to a Deputy's Sword with an attack 1 higher by researching at the Jail.");
	addnav("Return to the Jail","runmodule.php?module=$jail");
	villagenav();
}
?>