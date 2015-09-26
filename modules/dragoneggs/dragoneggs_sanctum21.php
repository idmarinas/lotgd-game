<?php
function dragoneggs_sanctum21(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Order of the Inner Sanctum");
	output("`n`c`b`&Order of the Inner Sanctum`b`c`7");
	$chance=e_rand(1,10) - $op2;
	$level=$session['user']['level'];
	$session['user']['hitpoints']*=.75;
	if ($op2==0){
		$lose=2;
		$session['user']['gems']-=2;
		output("You use `%2 gems`7 to help with the dragon egg destroying spell...`n`n");
	}else{
		$lose=1;
		$session['user']['gems']--;
		output("You use `%1 gem`7 to help with the dragon egg destroying spell...`n`n");
	}
	if (($level>10 && $chance<=3) || ($level<=10 && $chance<=2)){
		output("You successfully destroy the dragon egg! All the members of the Order come and congratulate you.`n`n");
		output("Your `&charm increases by 5`7 and you `&gain a Dragon Egg Point`7.");
		increment_module_pref("dragoneggs",1,"dragoneggpoints");
		increment_module_pref("dragoneggshof",1,"dragoneggpoints");
		debuglog("spent $lose gems to gain 5 charm and destroy a dragon egg during a research turn at the Order of the Inner Sanctum.");
		addnews("`7Thanks to the work of %s`7, a dragon egg was destroyed thereby saving many lives!",$session['user']['name']);
	}else{
		$op2++;
		if ($session['user']['gems']>0){
			output("You fail... but there's still time... If you want to try again you may do it if you spend a `%gem`7...");
			output("`n`nEach time you try, you have a much higher chance of succeeding.");
			addnav("Attempt to Destroy the Dragon Egg Again","runmodule.php?module=dragoneggs&op=sanctum21&op2=$op2");
		}else output("You fail to destroy the dragon egg and there's nothing left for you to do.");
		debuglog("spent $lose gems trying to destroy a dragon egg during a research turn at the Order of the Inner Sanctum.");
	}
	addnav("Return to the Order","runmodule.php?module=sanctum");
	villagenav();
}
?>