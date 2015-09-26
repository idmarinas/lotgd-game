<?php
function dragoneggs_rock7(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("The Veteran's Club");
	output("`c`b`2The Veteran's Club`b`c`4`n`n");
	if ($op2>=1){
		if ($op2==1){
			output("You choose to work on your specialty, and soon enough you've improved!`n`n");
			require_once("lib/increment_specialty.php");
			increment_specialty("`&");
			debuglog("spent 3 turns to increment specialty while researching dragon eggs at the Curious Looking Rock.");
		}elseif ($op2==2){
			$session['user']['gems']+=2;
			output("You choose to learn how to find gems, and soon enough you've found `%2 gems`4!!");
			debuglog("spent 3 turns to gain 2 gems while researching dragon eggs at the Curious Looking Rock.");
		}else{
			output("You learn some special training techniques and your hitpoints improves!");
			$session['user']['maxhitpoints']++;
			debuglog("spent 3 turns to gain 1 permanent hitpoints while researching dragon eggs at the Curious Looking Rock.");
		}
		addnav("Return to the Curious Looking Rock","rock.php");
		villagenav();
	}else{
		$session['user']['turns']-=3;
		$chance=e_rand(1,9);
		$level=$session['user']['level'];
		if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
			output("After spending `@3 turns`4 with the stranger, you find you're learning more than you ever thought.`n`n");
			output("`^'Excellent.  Now, let's use your knowledge.  What would you like to learn more about?'");
			output("`n`n`4He offers you a choice to do 1 of 3 things: `n`n `&Increase your Specialty`4`n `%Collect 2 gems`4`n`\$Increase your Permanent hitpoints by 1");
			output("`n`n`4What would you like to do?");
			addnav("Learn");
			addnav("Increase Specialty","runmodule.php?module=dragoneggs&op=rock7&op2=1");
			addnav("Gain 2 gems","runmodule.php?module=dragoneggs&op=rock7&op2=2");
			addnav("Improve hitpoints by 1","runmodule.php?module=dragoneggs&op=rock7&op2=3");
		}else{
			output("After spending `@3 turns`4 working with the stranger, he throws up his hands in frustration.");
			output("`n`n`^'No! I cannot teach you.  You just don't have the ability to concentrate on what I am saying.  Perhaps next time you will be able to understand what I'm teaching.'");
			output("`n`n`4You leave, very disappointed. Your adrenaline gives you back one of your turns.");
			$session['user']['turns']++;
			debuglog("spent 2 turns while researching dragon eggs at the Curious Looking Rock.");
			addnav("Return to the Curious Looking Rock","rock.php");
			villagenav();
		}
	}
}
?>