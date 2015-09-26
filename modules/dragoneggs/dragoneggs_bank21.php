<?php
function dragoneggs_bank21(){
	global $session;
	page_header("Ye Olde Bank");
	$hps=$session['user']['hitpoints'];
	$level=$session['user']['level'];
	output("`\$`c`b~ ~ ~ Fight ~ ~ ~`b`c`n`@You have encountered `^Bank Robbers`@ which lunges at you with `%Large Swords`@!");
	output("`n`n`2Level: `6%s`n`2`bStart of round:`b`nBank Robbers's Hitpoints: `6%s`2`nYOUR Hitpoints: `6%s`^`n",$level,$hps+4,$hps);
	output("`bBank Robbers`\$ surprises you and gets the first round of attack!`b`n`n");
	$chance=e_rand(1,5);
	if (($level>4 && $chance<=4) || ($level<=4 && $chance<=2)){
		output("`^Bank Robbers`4 tries to hit you but you `^RIPOSTE`4 for `^%s `4points of damage!`n",$hps+e_rand(2,12));
		output("`b`&You disarm them and the authorities swarm in.`n");
		output("`\$You have defeated Bank Robbers!`n`n`b");
		output("`3You recover and the staff admires your skills. The bank manager shakes your hand and you `&gain 2 charm`3.");
		$session['user']['charm']+=2;
		debuglog("gained 2 charm by researching at the Bank.");
	}else{
		if ($hps>1) output("`^Bank Robbers`4 hits you for `\$%s`4 points of damage!`n",$hps-1);
		else output("`^Bank Robbers`4 is about to hit you when you pass out.`n");
		output("`&Just before the Bank Robbers have a chance to hit you again, the bank manager intervenes and gives them access to the vault.");
		output("`n`nThe Robbers steal all the money from everyone in the bank (including you!) and you are left with only 1 hitpoint.");
		$session['user']['hitpoints']=1;
		$gold=$session['user']['gold'];
		$session['user']['gold']=0;
		debuglog("all hitpoints except 1 and $gold gold by researching at the Bank.");
	}
	addnav("Continue Banking","bank.php");
	villagenav();
}
?>