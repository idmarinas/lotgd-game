<?php
function dragoneggs_armor25(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Pegasus Armor");
	output("`c`b`%Pegasus Armor`b`c`5");
	output("You trade your armor and take the book.  You read it over and a spell is cast creating `%%s gems`5.",$op2);
	$def=$session['user']['armordef'];
	$session['user']['gems']+=$op2;
	$session['user']['defense']-=$session['user']['armordef'];
	$session['user']['armordef']=1;
	$session['user']['defense']++;
	$session['user']['armor']="T-Shirt";
	$session['user']['armorvalue']=0;
	debuglog("sold armor ($def defense) for $op2 gems while researching dragon eggs at Pegasus Armor.");
	addnav("Return to Pegasus Armor","armor.php");
	villagenav();
}
?>