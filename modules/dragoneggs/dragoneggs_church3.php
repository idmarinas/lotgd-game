<?php
function dragoneggs_church3(){
	global $session;
	page_header("Old Church");
	$hps=$session['user']['hitpoints'];
	$level=$session['user']['level'];
	output("`\$`c`b~ ~ ~ Fight ~ ~ ~`b`c`n`@You have encountered `^Capelthwaite`@ which lunges at you with `%Giant Cross`@!");
	output("`n`n`2Level: `6%s`n`2`bStart of round:`b`nCapelthwaite's Hitpoints: `6%s`2`nYOUR Hitpoints: `6%s`^`n",$level,$hps+4,$hps);
	output("`bCapelthwaite`\$ surprises you and gets the first round of attack!`b`n`n");
	$chance=e_rand(1,5);
	if (($level>4 && $chance<=4) || ($level<=4 && $chance<=2)){
		output("`^Capelthwaite`4 tries to hit you but you `^RIPOSTE`4 for `^%s `4points of damage!`n",$hps+e_rand(100,500));
		output("`&You bat his cross away and shake your head.  Clearly, the clergy are not a match for someone with as much faith as you have in your %s`&.`n",$session['user']['weapon']);
		output("`\$You have defeated Capelthwaite!`n`n");
		output("You better leave.  You're not sure what's going on here, but it's time to get out of here.");
	}else{
		if ($hps>1) output("`^Capelthwaite`4 hits you for `\$%s`4 points of damage!`n",$hps-1);
		else output("`^Capelthwaite`4 raises his Cross to whack you but your cringing shaking shape causes him to hesitate.`n");
		output("`^'Get out of here you Demon!'`3 he cries out.  Realizing that a man with a blunt cross can be quite intimidating, you decide it's time to leave.");
		$session['user']['hitpoints']=1;
		debuglog("all hitpoints except 1  by researching at the Church.");
	}
	villagenav();
}
?>