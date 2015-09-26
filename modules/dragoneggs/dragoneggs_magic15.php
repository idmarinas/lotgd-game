<?php
function dragoneggs_magic15(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	if ($op2==1){
		page_header("Healer's Hut");
		output("`c`b`#Healer's Hut`b`c`n");
		if ($session['user']['gems']>0){
			output("You give a `%gem`# and the doctor gives you a paper acknowledging completion of this part of the Gift Shop Quest.");
			set_module_pref("quest2",2);
			$session['user']['gems']--;
			output("`n`nYou try to remember where you have to go to next.");
			debuglog("gave a gem at the Healer's Hut to advance Ye Ol' Gifte Shoppe Quest.");
		}else{
			output("You don't have a `%gem`# to complete this part of the quest.");
		}
		require_once("lib/forest.php");
		forest(true);
	}elseif ($op2==2){
		page_header("Ye Olde Bank");
		output("`n`c`b`^Ye Olde Bank`b`c`6");
		if ($session['user']['gems']>0){
			output("You give a `%gem`6 and the banker gives you a paper acknowledging completion of this part of the Gift Shop Quest.");
			set_module_pref("quest2",3);
			$session['user']['gems']--;
			debuglog("gave a gem at the Bank to advance Ye Ol' Gifte Shoppe Quest.");
			output("`n`nYou try to remember where you have to go to next.");
		}else{
			output("You don't have a `%gem`6 to complete this part of the quest.");
		}
		addnav("Continue Banking","bank.php");
		villagenav();
	}elseif ($op2==3){
		page_header("Daily News");
		output("`n`c`b`!Daily News`b`c`@`n");
		if ($session['user']['gems']>0){
			output("You give a `%gem`@ and the editor gives you a paper acknowledging completion of this part of the Gift Shop Quest.");
			set_module_pref("quest2",4);
			$session['user']['gems']--;
			debuglog("gave a gem at the Daily News to advance Ye Ol' Gifte Shoppe Quest.");
			output("`n`nYou try to remember where you have to go to next.");
		}else{
			output("You don't have a `%gem`@ to complete this part of the quest.");
		}
		addnav("Return to the Daily News","news.php");
		villagenav();
	}elseif ($op2==4){
		$storename=get_module_setting('gsowner','pqgiftshop');
		$header = color_sanitize($storename);
		page_header("%s's Ol' Gifte Shoppe",$header);
		output("`c`b`&Ye Ol' Gifte Shoppe`b`c`7`n`n");
		set_module_pref("quest2",5);
		output("%s`7 reads a spell out loud and you feel a strange power flush over you.",$storename);
		output("`n`nYou `&Gain an attack`7, `&Gain a defense`7, and `&Gain 5 charm`7.");
		$session['user']['attack']++;
		$session['user']['defense']++;
		$session['user']['charm']+=5;
		debuglog("gained an attack, a defense, and 5 charm for completing Ye Ol' Gifte Shoppe Quest.");
		addnav(array("Return to %s's Gift Shop",get_module_setting('gsowner','pqgiftshop')),"runmodule.php?module=pqgiftshop");
		villagenav();
	}
}
?>