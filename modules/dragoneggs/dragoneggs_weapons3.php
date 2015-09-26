<?php
function dragoneggs_weapons3(){
	global $session;
	page_header("MightyE's Weapons");
	output("`c`b`&MightyE's Weapons`b`c`7");
	$session['user']['gold']+=350;
	$session['user']['turns']--;
	output("You settle back to watch the store, greeting other warriors with a nod of your head.  Sadly, sales are a little lacking.`n`n");
	output("Soon enough, `!MightyE`7 comes back and pays you `^350 gold`7 and says thank you.");
	debuglog("spent 1 turn to gain 350 gold by researching at the MightyE's Weapons.");
	addnav("Return to MightyE's Weapons","weapons.php");
	villagenav();
}
?>