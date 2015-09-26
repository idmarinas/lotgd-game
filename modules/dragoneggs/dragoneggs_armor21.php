<?php
function dragoneggs_armor21(){
	global $session;
	page_header("Pegasus Armor");
	output("`c`b`%Pegasus Armor`b`c`5");
	output("You sell your armor and pocket the `^13,000 gold`5.");
	$def=$session['user']['armordef'];
	$session['user']['gold']+=13000;
	$session['user']['defense']-=$session['user']['armordef'];
	$session['user']['armordef']=1;
	$session['user']['defense']++;
	$session['user']['armor']="T-Shirt";
	$session['user']['armorvalue']=0;
	debuglog("sold armor ($def defense) for 13,000 gold while researching dragon eggs at Pegasus Armor.");
	addnav("Return to Pegasus Armor","armor.php");
	villagenav();
}
?>