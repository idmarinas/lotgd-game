<?php
function dragoneggs_town15(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$header = color_sanitize($vname);
	page_header("%s Square",$header);
	output("`c`b`@%s Square`@`b`c",$vname);
	output("You use `%5 gems`@ to successfully destroy a dragon egg! Congratulations.  You `&gain a Dragon Egg Point`@.");
	increment_module_pref("dragoneggs",1,"dragoneggpoints");
	increment_module_pref("dragoneggshof",1,"dragoneggpoints");
	$session['user']['gems']-=5;
	addnews("%s`@ destroyed a dragon egg in the Capital Town Square.",$session['user']['name']);
	debuglog("used 5 gems to destroy a dragon egg in the Capital Town Square and gain a dragon egg point.");
	villagenav();
}
?>