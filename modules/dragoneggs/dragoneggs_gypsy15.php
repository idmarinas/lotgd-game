<?php
function dragoneggs_gypsy15(){
	global $session;
	page_header("Gypsy Seer's Graveyard");
	$hps=$session['user']['hitpoints'];
	$level=$session['user']['level'];
	output("`\$`c`b~ ~ ~ Fight ~ ~ ~`b`c`n`@You have encountered `^Vampire`@ which lunges at you with `%Pointy Teeth`@!");
	output("`n`n`2Level: `6%s`n`2`bStart of round:`b`nVampire's Hitpoints: `6%s`2`nYOUR Hitpoints: `6%s`^`n",$level,$hps+4,$hps);
	output("`bVampire`\$ surprises you and gets the first round of attack!`b`n`n");
	$chance=e_rand(1,5);
	if (($level>4 && $chance<=4) || ($level<=4 && $chance<=2)){
		output("`^Vampire`4 tries to hit you but you `^RIPOSTE`4 for `^%s `4points of damage!`n",$hps+e_rand(55,166));
		output("`b`&The Vampire falls at your feet... Dead... err... more dead... errr... dead again... You hack off its head. SUPER DEAD!`n");
		output("`\$You have defeated a Vampire!`n`n`b");
		output("`3You brush yourself off and feel rather proud.  You grab one of the fangs as a souvenir and it makes you more charming.");
		output("`n`nYou `&gain 1 charm`3 and a `%gem`3.");
		$session['user']['charm']++;
		$session['user']['gems']++;
		debuglog("gained 1 charm and 1 gem by researching at the Gypsy Seer's Tent.");
	}else{
		if ($hps>1) output("`^Vampire`4 hits you for `\$%s`4 points of damage!`n",$hps-1);
		else output("`^Vampire`4 is about to hit you when you pass out.`n");
		output("`&It starts feeding off of you... you feel your life ebbing.  You are about to die.");
		output("`n`n`5However, the sun starts to rise and the `\$Vampire`5 must retreat.  It steals your `^gold`5.  You are left with only `\$1 hitpoint`5.");
		$session['user']['hitpoints']=1;
		$gold=$session['user']['gold'];
		$session['user']['gold']=0;
		debuglog("all hitpoints except 1 and $gold gold by researching at the Gypsy Seer's Tent.");
	}
	addnav("Return to the Gypsy Seer's Tent","gypsy.php");
	villagenav();
}
?>