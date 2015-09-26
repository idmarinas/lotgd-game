<?php
function dragoneggs_heidi17(){
	global $session;
	page_header("Heidi's Place");
	output("`c`b`&Heidi, the Well-wisher`b`c`7`n");
	$chance=e_rand(1,5);
	output("You take the statue and start to look at it.  The `&Heidi`7 smiles at you.`n`n");
	if (($session['user']['level']>8 && $chance<4) || ($session['user']['level']<9 && $chance<3)){
		output("`@'This may have strange powers, especially if you hold it like you are right now...'`7 she says.");
		output("`n`nYou feel a strange flush run through your body.  Your memory fades... your upbringing comes into question.");
		output("`n`nSoon enough, you feel like you realize you're no longer who you used to be.  You're race has been cleared!");
		output("`n`nOn your next newday, you'll be able to choose a new one.");
		$session['user']['race']=RACE_UNKNOWN;
		debuglog("reset race while researching dragon eggs at Heidi's Place.");
		addnav("Return to Heidi's Place","runmodule.php?module=heidi");
	}else{
		output("You drop the statue.  `&Heidi`7 stops smiling at you.  You decide to leave.");
	}
	villagenav();
}
?>