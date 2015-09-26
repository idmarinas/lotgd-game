<?php
function dragoneggs_animal13(){
	global $session;
	page_header("Pegasus Armor");
	output("`c`b`%Pegasus Armor`b`c`5`n");
	output("You offer your `^Antique Lantern`5 to `#Pegasus`5.  She takes a look at it.");
	if (get_module_pref("lantern")==1){
		output("`#'This is in very nice condition,'`5 she comments. `#'I can offer you `^100 gold`# for it, are you interested?'`5");
		if ($session['user']['level']>6){
			output("You negotiate a higher price with her, finally agreeing to `^250 gold`5.");
			$session['user']['gold']+=250;
			debuglog("sold a lantern for 250 gold while researching dragon eggs at Merick's Stables.");
		}else{
			output("You take what you can get for it and happily accept the money.");
			$session['user']['gold']+=100;
			debuglog("sold a lantern for 100 gold while researching dragon eggs at Merick's Stables.");
		}
	}else{
		output("`#'This lantern looks like you've been dragging it around to battles, perhaps against `@Green Dragons`#.  It's in horrible condition! Here's `^10 gold`#, that's the best you'll get for it.'");
		output("`n`n`5You take the money and grumble while handing over the lantern.");
		$session['user']['gold']+=10;
		debuglog("sold a lantern for 10 gold while researching dragon eggs at Merick's Stables.");
	}
	set_module_pref("lantern",0);
	addnav("Return to Pegasus Armor","armor.php");
	villagenav();
}
?>