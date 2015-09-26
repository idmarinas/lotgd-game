<?php
function dragoneggs_jewelry17(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Oliver, the Jeweler");
	output("`c`b`&Oliver's Jewelry`b`c`7`n");
	if ($op2==1){
		if (e_rand(1,3)==1){
			output("You step into the light and find yourself in the shades!");
			$session['user']['hitpoints']=0;
			$session['user']['deathpower']+=100;
			output("You have an extra 100 deathpoints... so it's kind of like you're just touring the Shades and you can return to the real world at any time.  Err... Kind of.");
			addnav("Shades","shades.php");
			debuglog("Got sent to the shades with 100 extra deathpower while researching dragon eggs at the Jeweler.");
		}else{
			output("You bump into a lightbulb.  DUH!");
			addnav("Return to Oliver's Jewelry","runmodule.php?module=jeweler");
			villagenav();
		}
	}else{
		if (e_rand(1,20)==1)output("That's kind of boring.  Okay then. I mean, I spend all this time writing these events and you don't even bother trying it out?? Fine fine. Whatever.  I don't care.  <sniffle>");
		else output("Not believing there's anything to the light you leave.");
		addnav("Return to Oliver's Jewelry","runmodule.php?module=jeweler");
		villagenav();
	}
}
?>