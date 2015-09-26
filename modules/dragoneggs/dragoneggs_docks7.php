<?php
function dragoneggs_docks7(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("The Docks");
	output("`c`b`^The Docks`b`c`7`n");
	if (is_module_active("oceanquest")) $docks="oceanquest";
	else $docks="docks";
	$amount=$op3*$op2;
	$session['user']['turns']-=$op2;
	$session['user']['gold']+=$amount;
	debuglog("spent $op2 turns to make $amount money while researching dragon eggs at the Docks.");
	output("You decide to get to work for some easy money. Well, it turns out not to be so easy.  This is real work.");
	output("You put in your time and work for `@%s %s`7 and earn `^%s gold`7.  Not bad!",$op2,translate_inline($op2>1?"turns":"turn"),$amount);
	addnav("Return to the Docks","runmodule.php?module=$docks&op=docks&op2=enter");
	addnav("Return to the Forest","forest.php");
}
?>