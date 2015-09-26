<?php
function dragoneggs_magic7(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$storename=get_module_setting('gsowner','pqgiftshop');
	$header = color_sanitize($storename);
	page_header("%s's Ol' Gifte Shoppe",$header);
	output("`c`b`&Ye Ol' Gifte Shoppe`b`c`7`n`n");
	$session['user']['gold']-=$op2;
	output("You hand over the `^%s gold`7 and walk over to a corner.  You pin the pendant on your shirt and feel empowered.",$op2);
	output("`n`nYour armor's defense improves by 2 and starts glowing.");
	$session['user']['armordef']+=2;
	$session['user']['armor']="Glowing ".$session['user']['armor'];
	$session['user']['armorvalue']+=$op2;
	debuglog("has glowing armor from a magic pendant purchased while researching dragon eggs at Ye Ol' Gifte Shoppe.");
	addnav(array("Return to %s's Gift Shop",get_module_setting('gsowner','pqgiftshop')),"runmodule.php?module=pqgiftshop");
	villagenav();
}
?>