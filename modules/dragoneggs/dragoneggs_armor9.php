<?php
function dragoneggs_armor9(){
	global $session;
	page_header("Pegasus Armor");
	output("`c`b`%Pegasus Armor`b`c`5");
	output("You take a bite of the crunchy cookie. You gain a `\$Permanent hitpoint`5.`n`nYou read the cookie:`n`n");
	if (e_rand(1,4)>1){
		output("`\$`cYou will Lose a Permanent hitpoint`c`n`5");
		output("Bottom line: Nothing lost, nothing gained.");
	}else{
		output("`@`cYou will have a nice day.`c`n`5");
		output("You gain an `@extra turn`5 from such a nice fortune!");
		$session['user']['turns']++;
		$session['user']['maxhitpoints']++;
		debuglog("gained a turn and a max permanent hitpoint while researching dragon eggs at Pegasus Armor.");
	}
	addnav("Return to Pegasus Armor","armor.php");
	villagenav();
}
?>