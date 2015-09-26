<?php
function dragoneggs_armor11(){
	global $session;
	page_header("Pegasus Armor");
	output("`c`b`%Pegasus Armor`b`c`5");
	output("You reach into the jar chanting `)'Black, black, black'`5...`n`n");
	if (e_rand(1,6)>1){
		output("Nope... it's `&white`5.  No good.");
	}else{
		output("It's a `)Black Marble`5!! Yay!");
		$session['user']['gold']+=200;
		debuglog("gained 200 gold while researching dragon eggs at Pegasus Armor.");
	}
	addnav("Return to Pegasus Armor","armor.php");
	villagenav();
}
?>