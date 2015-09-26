<?php
	if (get_module_setting("notice")==1 && get_module_pref("notice")==0 && $session['user']['dragonkills']>=get_module_setting("mindk")){
		require_once("lib/systemmail.php");
		$subj = sprintf("The Hunt for Dragon Eggs");
		$body = sprintf("`^Congratulations!`n`nYou have killed the `@Green Dragon`^ enough times that something deep in your mind begins to remember something... `&Dragon Eggs`^!`n`nYou're never going to get rid of the menace of the `@Green Dragon`^ as long as `&Dragon Eggs`^ exist in the kingdom.`n`nYou now have gained the ability to search out these `&Dragon Eggs`^ in order to destroy them.  There are many places that you'll be able to search for these eggs.  As you become more experienced, even more places will open up.  In addition, you may be able to search for `&Dragon Eggs`^ at new locations if you help support the site.`n`nPlease help! Perhaps one day the kingdom will be safe again!  There are still many new things to learn and explore.");
		systemmail($session['user']['acctid'],$subj,$body);
		set_module_pref("notice",1);
	}
	if (get_module_pref("quest1")>0) set_module_pref("quest1",0);
	if (get_module_pref("quest2")>0 && get_module_pref("quest2")<5) set_module_pref("quest2",0);
	if (get_module_setting("reset")==0) set_module_pref("researches",0);
	if (get_module_pref("puzzlepiece")==2){
		set_module_pref("puzzlepiece",0);
		output("`n`7You notice `&Heidi`7 throwing away some garbage... Was that the puzzle you were trying to finish? Oh well, you toss away the puzzle piece.`n");
	}
	if (get_module_pref("retainer")==2){
		$retain=e_rand(1,8);
		if ($retain==1){
			set_module_pref("retainer",0);
			output("`n`5You lose your retainer due to a paperwork mistake.`n`0");
			debuglog("lost their retainer.");
		}else{
			if ($retain==2){
				$gold=round(get_module_setting("retainerpay")*1.5);
				output("`n`5You get a bonus from the retainer today and receive `^%s gold`5.`n",$gold);
				$session['user']['gold']+=$gold;
				debuglog("gained $gold gold from their retainer today.");
			}elseif ($retain<5){
				output("`n`5The retainer doesn't pay today.  That's unfortunate because it'd be really nice to get the extra gold, wouldn't it?`n");
				debuglog("didn't get any gold from their retainer today.");
			}else{
				$gold=get_module_setting("retainerpay");
				output("`n`5You get your standard retainer of `^%s gold`5 today.`n",$gold);
				$session['user']['gold']+=$gold;
				debuglog("gained $gold gold from their retainer today.");
			}
			set_module_pref("retainer",1);
		}
	}
	if (get_module_pref("inform")==1){
		output("`n`&`bAll locations are open for research today.`b`n");
		set_module_pref("inform",0);
	}
?>