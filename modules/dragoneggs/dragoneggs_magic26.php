<?php
function dragoneggs_magic26(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$storename=get_module_setting('gsowner','pqgiftshop');
	$header = color_sanitize($storename);
	page_header("%s's Ol' Gifte Shoppe",$header);
	output("`c`b`&Ye Ol' Gifte Shoppe`b`c`7`n`n");
	$session['user']['gold']-=1000;
	output("As soon as you give %s`7 the `^1000 gold`7 she casts a spell and you feel your power increase.",$storename);
	output("`n`n`7You Advance in your Specialty`7.");
	require_once("lib/increment_specialty.php");
	increment_specialty("`&");
	debuglog("incremented Specialty for 1000 gold at Ye Ol' Gifte Shoppe.");
	addnav(array("Return to %s's Gift Shop",get_module_setting('gsowner','pqgiftshop')),"runmodule.php?module=pqgiftshop");
	villagenav();
}
?>