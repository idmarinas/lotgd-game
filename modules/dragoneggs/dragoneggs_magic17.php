<?php
function dragoneggs_magic17(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$storename=get_module_setting('gsowner','pqgiftshop');
	$header = color_sanitize($storename);
	page_header("%s's Ol' Gifte Shoppe",$header);
	output("`c`b`&Ye Ol' Gifte Shoppe`b`c`7`n`n");
	increment_module_pref("dragoneggs",-$op2,"dragoneggpoints");
	set_module_pref("exchange",1);
	$session['user']['maxhitpoints']+=$op2;
	output("You agree to the exchange.  You present your `&%s Dragon Egg %s`7 and %s`7 casts a spell to give you `\$%s permanent %s`7.",$op2,translate_inline($op2>1?"Points":"Point"),$storename,$op2,translate_inline($op2>1?"hitpoints":"hitpoint"),$storename);
	output("`n`n`&'Thank you very much.  I think my spell can be completed now,'`7 says %s`7.",$storename);
	debuglog("gained $op2 permanent hitpoints for $op2 dragon egg points while researching dragon eggs at Ye Ol' Gifte Shoppe.");
	addnav(array("Return to %s's Gift Shop",get_module_setting('gsowner','pqgiftshop')),"runmodule.php?module=pqgiftshop");
	villagenav();
}
?>