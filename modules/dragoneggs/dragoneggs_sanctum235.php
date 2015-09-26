<?php
function dragoneggs_sanctum235(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Order of the Inner Sanctum");
	$hps=$session['user']['hitpoints'];
	$level=$session['user']['level'];
	output("`\$`c`b~ ~ ~ Fight ~ ~ ~`b`c`n`@You have encountered `^Ythilian`@ which lunges at you with `%Poisoned Bite`@!");
	output("`n`n`2Level: `6%s`n`2`bStart of round:`b`nYthilian's Hitpoints: `6%s`2`nYOUR Hitpoints: `6%s`^`n",$level,$hps+4,$hps);
	output("`bYthilian`\$ surprises you and gets the first round of attack!`b`n`n");
	$chance=e_rand(1,5);
	if (($level>4 && $chance<=4) || ($level<=4 && $chance<=2)){
		output("`^Ythilian`4 tries to hit you but you `^RIPOSTE`4 for `^%s `4points of damage!`n",$hps+e_rand(2,12));
		output("`\$You have defeated Ythilian!`n`n`b");
		output("`3YBy drinking its blood you gain a permanent hitpoint!");
		$session['user']['maxhitpoints']++;
		debuglog("gained a permanent hitpoint by researching at the Inner Sanctum.");
	}else{
		if ($hps>1) output("`^Ythilian`4 hits you for `\$%s`4 points of damage!`n",$hps-1);
		else output("`^Ythilian`4 is about to hit you when you pass out.`n");
		output("`&Just before the `QYthilian`& can try to ingest you, other Order members burst in and rescue you.");
		output("`n`nThe experience has taught you something; you `%gain a gem`& but you are left with only 1 hitpoint.");
		$session['user']['hitpoints']=1;
		$session['user']['gems']++;
		debuglog("all hitpoints except 1 and gained 1 gem by researching at the Inner Sanctum.");
	}
	addnav("Return to the Order","runmodule.php?module=sanctum");
	villagenav();
}
?>