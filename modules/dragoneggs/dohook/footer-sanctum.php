<?php
	if ($op=="" && get_module_pref("member","sanctum")>=0){
		addnav("Dragon Egg Research");
		addnav("`@Search the Inner Sanctum","runmodule.php?module=dragoneggs&op=sanctum");
		if (get_module_pref("quest1")==5){
			output("`n`n`n`n`7The finely dressed man congratulates you on completing the quest.");
			output("`n`nYou receive a `%gem`7 as a reward.");
			set_module_pref("quest1",0);
			$session['user']['gems']++;
			debuglog("gains a gem for completing a quest by searching for dragon eggs at the Inner Sanctum.");
		}
	}
?>