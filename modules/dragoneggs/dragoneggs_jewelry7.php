<?php
function dragoneggs_jewelry7(){
	global $session;
	page_header("Oliver, the Jeweler");
	output("`c`b`&Oliver's Jewelry`b`c`7`n");
	$hps=$session['user']['hitpoints'];
	$level=$session['user']['level'];
	$chance=e_rand(1,5);
	if (($level>4 && $chance<=4) || ($level<=4 && $chance<=2)){
		if ($session['user']['weapon']!="`#Ceremonial Dagger +2`0" && e_rand(1,3)==1){
			output("You pull out a `#Ceremonial Dagger`7... and a nice one at that! It's `&2 attack`7 better than your %s`7.",$session['user']['weapon']);
			$session['user']['weapon']="`#Ceremonial Dagger +2`0";
			$session['user']['weapondmg']+=2;
			$session['user']['attack']+=2;
			debuglog("gained a ceremonial dagger (+2 over current weapon) while researching dragon eggs at Oliver's Jewelry.");
		}else{
			output("You find a gem.  Excellent!");
			output("`n`nYou `%gain a gem`7.");
			$session['user']['gems']++;
			debuglog("gained a gem while researching dragon eggs at Oliver's Jewelry.");
		}
	}else{
		$debug="lost half hitpoints";
		output("Something grabs your hand and starts gnawing on it!!! You feel a deep bite and cringe.`n`n");
		if ($level>3 && $session['user']['maxhitpoints']>$level*11 && e_rand(1,2)==1){
			output("You lose a `\$Permanent hitpoint`7 from the pain.");
			$debug.=", lost a permanent hitpoint";
		}
		if ($session['user']['gems']>0){
			output("You lose a `%gem`7 in frustration.");
			$debug.=",lost a gem";
			$session['user']['gems']--;
		}
		output("You're `\$hitpoints points`7 are cut in half.");
		$session['user']['hitpoints']*=.5;
		debuglog("$debug while researching dragon eggs at Oliver's Jewelry.");
	}
	addnav("Return to Oliver's Jewelry","runmodule.php?module=jeweler");
	villagenav();
}
?>