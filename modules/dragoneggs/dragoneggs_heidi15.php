<?php
function dragoneggs_heidi15(){
	global $session;
	page_header("Heidi's Place");
	$hps=$session['user']['hitpoints'];
	$level=$session['user']['level'];
	output("`\$`c`b~ ~ ~ Fight ~ ~ ~`b`c`n`@You have encountered `^Pick Pocket`@ which lunges at you with `%Sticky Fingers`@!");
	output("`n`n`2Level: `6%s`n`2`bStart of round:`b`nPick Pocket's Hitpoints: `6%s`2`nYOUR Hitpoints: `6%s`^`n",$level,$hps+1,$hps);
	output("`bPick Pocket`\$ surprises you and gets the first round of attack!`b`n`n");
	$chance=e_rand(1,5);
	if (($level>4 && $chance<=4) || ($level<=4 && $chance<=2)){
		output("`^Pick Pocket`4 tries to hit you but you `^RIPOSTE`4 for `^%s `4points of damage!`n`n",$hps+e_rand(0,2));
		output("`b`&You have defeated the Pick Pocket.  You give him a dirty scowl and kick him out of Heidi's Place.");
		debuglog("gained 1 turn by researching at Heidi's Place.");
	}else{
		if ($hps>1) output("`^Pick Pocket`4 hits you for `\$%s`4 points of damage!",$hps-1);
		else output("`^Pick Pocket`4 is about to hit you cower in fear.");
		output("`n`n`#'Please don't kill me!'`7 you cry out.  The Pick Pocket takes pity on you.`n`n");
		$session['user']['hitpoints']=1;
		$gold=$session['user']['gold'];
		$gems=$session['user']['gems'];
		if ($session['user']['gold']>250){
			output("He takes all your money and leaves you with just `\$1 hitpoint`7.");
			$session['user']['gold']=0;
			debuglog("gained 1 turn but lost all hitpoints except 1 and $gold gold by researching at Heidi's Place.");
		}elseif ($session['user']['gems']>1){
			output("He takes `%2 of your gems`7 and leaves you with just `\$1 hitpoint`7.");
			$session['user']['gems']-=2;
			debuglog("gained 1 turn but lost all hitpoints except 1 and 2 gems by researching at Heidi's Place.");
		}else{
			output("You are left with a horrible black eye. The pickpocket takes all your money and gems and leaves you with just `\$1 hitpoint`7.");
			$session['user']['gold']=0;
			$session['user']['gems']=0;
			debuglog("gained 1 turn but lost all hitpoints except 1 and $gems gems and $gold gold by researching at Heidi's Place.");
		}
	}
	addnav("Return to Heidi's Place","runmodule.php?module=heidi");
	villagenav();
}
?>