<?php
function dragoneggs_police25(){
	global $session;
	page_header(array("The Jail of %s", $session['user']['location']));
	output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
	if (is_module_active("jail")) $jail="jail";
	else $jail="djail";
	output("You exchange your `&Dragon Egg Point`2 for `^500 gold`2 and thank the sheriff.");
	$session['user']['gold']+=500;
	increment_module_pref("dragoneggs",-1,"dragoneggpoints");
	debuglog("exchanged a dragon egg point for 500 gold by researching at the Jail.");
	addnav("Return to the Jail","runmodule.php?module=$jail");
	villagenav();
}
?>