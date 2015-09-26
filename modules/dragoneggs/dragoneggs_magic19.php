<?php
function dragoneggs_magic19(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$storename=get_module_setting('gsowner','pqgiftshop');
	$header = color_sanitize($storename);
	page_header("%s's Ol' Gifte Shoppe",$header);
	output("`c`b`&Ye Ol' Gifte Shoppe`b`c`7`n`n");
	$session['user']['gems']-=$op2;
	if ($op2==1){
		$session['user']['turns']++;
		$turns=1;
	}elseif ($op2==2){
		$session['user']['turns']+=3;
		$turns=3;
	}elseif ($op2==3){
		$session['user']['turns']+=5;
		$turns=5;
	}
	output("You agree to the exchange.  You give `%%s %s`7 and %s`7 casts a spell to give you `@%s %s`7.",$op2,translate_inline($op2>1?"Gems":"Gem"),$storename,$turns,translate_inline($turns>1?"Turns":"Turn"));
	output("`n`n`&'Thank you very much.'`7 says %s`7.",$storename);
	debuglog("gained $turns turns for $op2 gems while researching dragon eggs at Ye Ol' Gifte Shoppe.");
	addnav(array("Return to %s's Gift Shop",get_module_setting('gsowner','pqgiftshop')),"runmodule.php?module=pqgiftshop");
	villagenav();
}
?>