<?php
function dragoneggs_hospital1(){
	global $session;
	page_header("Healer's Hut");
	$hps=$session['user']['hitpoints'];
	$level=$session['user']['level'];
	output("`\$`c`b~ ~ ~ Fight ~ ~ ~`b`c`n`@You have encountered `^Creepy Zombie`@ which lunges at you with `%Almost-Falling-Off Hand`@!");
	output("`n`n`2Level: `6%s`n`2`bStart of round:`b`nCreepy Zombie's Hitpoints: `6%s`2`nYOUR Hitpoints: `6%s`^`n",$level,$hps+4,$hps);
	output("`bCreepy Zombie`\$ surprises you and gets the first round of attack!`b`n`n");
	$chance=e_rand(1,5);
	if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
		output("`^Creepy Zombie`4 tries to hit you but you `^RIPOSTE`4 for `^%s `4points of damage!`n",$hps+e_rand(5,18));
		output("`b`&You take his arm off with a quickly executed move to save your life.`n");
		output("`\$You have slain Creepy Zombie!`n`n`b");
		output("`3You recover and the healer admires your skills.  You `%are given a gem as a reward`3.");
		$session['user']['gems']++;
		debuglog("gains a gem by researching at Healer's Hut.");
	}else{
		if ($hps>1) output("`^Creepy Zombie`4 hits you for `\$%s`4 points of damage!`n",$hps-1);
		else output("`^Creepy Zombie`4 is about to hit you when you pass out.`n");
		output("`&Just before the Zombie has a chance to hit you again, the healer intervenes.");
		output("He hits the Zombie with his staff and apologizes to you.  He fixes you up to restore your hitpoints to what they were before you started your examination.");
	}
	require_once("lib/forest.php");
	forest(true);
}
?>