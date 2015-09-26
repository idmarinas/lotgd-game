<?php
function dragoneggs_town17(){
	global $session;
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$header = color_sanitize($vname);
	page_header("%s Square",$header);
	output("`c`b`@%s Square`@`b`c",$vname);
	output("You boldly grab the beverage and swallow it down in one gulp.");
	output("`n`nYou `\$gain one permanent hitpoint`@!");
	debuglog("gained one permanent hitpoint by researching in the Capital Town Square.");
	$session['user']['maxhitpoints']++;
	villagenav();
}
?>