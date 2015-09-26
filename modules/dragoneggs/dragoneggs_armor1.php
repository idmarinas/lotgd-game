<?php
function dragoneggs_armor1(){
	global $session;
	page_header("Pegasus Armor");
	output("`c`b`%Pegasus Armor`b`c`5");
	output("You stick around trying to collect the pieces of the broken visor when `#Pegasus`5 comes over to research.");
	output("`n`n`#'That will be `^500 gold`#,' `5 she tells you.  You're stuck.  You hand over the money.  Other people in the shop notice and admire your honesty.");
	output("`n`nYou `&Gain 7 Charm Points`5!");
	$session['user']['charm']+=7;
	$session['user']['gold']-=500;
	addnews("%s did something very noble today.  Honesty is the best policy and it shows!",$session['user']['name']);
	debuglog("gained 7 charm for 500 gold while researching dragon eggs at Pegasus Armor.");
	addnav("Return to Pegasus Armor","armor.php");
	villagenav();
}
?>