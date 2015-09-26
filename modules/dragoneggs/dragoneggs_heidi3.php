<?php
function dragoneggs_heidi3(){
	global $session;
	page_header("Heidi's Place");
	output("`c`b`&Heidi, the Well-wisher`b`c`7`n");
	switch(e_rand(1,8)){
		case 1: case 2: case 3: case 4: case 5: case 6:
			output("You yank on the lever vigorously. There is no pleasure in this and nothing happens.");
		break;
		case 7:
			output("You hear a loud `@<clunking>`7.  Since it's in green text, it's a good thing!");
			output("`n`nYou've somehow `&destroyed a dragon egg`7 and `&gained a Dragon Egg Point`7!!!");
			increment_module_pref("dragoneggs",1,"dragoneggpoints");
			increment_module_pref("dragoneggshof",1,"dragoneggpoints");
			debuglog("gained a dragon egg point while researching dragon eggs at Heidi's Place");
			addnews("%s somehow destroyed a dragon egg.  Somehow.  Nobody's really sure how.  How lucky is that?",$session['user']['name']);
		break;
		case 8:
			output("You hear a loud `\$<clunking>`7.  Since it's in red text, it's a bad thing.");
			output("`n`nThe lever hits you on the chin when you let go of it, jarring your jaw");
			switch(e_rand(1,3)){
				case 1:
					$exp=round(e_rand(1,4)/100*$session['user']['experience']);
					output("and causing your memory to fade.");
					if ($exp>0){
						output("`n`nYou `#lose `^%s`# experience points.",$exp);
						$session['user']['experience']-=$exp;
						debuglog("lost $exp experience while researching dragon eggs at Heidi's Place.");
					}else{
						output("`n`nLuckily, you don't really suffer any consequences.");
					}
				break;
				case 2:
					output("and knocking a tooth out.");
					output("`n`nYou `&lose `^1`& charm`7 because you are toothless.");
					require_once("lib/names.php");
					$newtitle = translate_inline("Toothless");
					$newname = change_player_title($newtitle);
					$session['user']['title'] = $newtitle;
					$session['user']['name'] = $newname;
					$session['user']['charm']--;
					debuglog("lost a charm and became Toothless while researching dragon eggs at Heidi's Place.");
				break;
				case 3:
					output("and causing you to forget something important.");
					if ($session['user']['gems']>0){
						output("`n`nWhere did you put that `%gem`7? You lose a `%gem`7.");
						$session['user']['gems']--;
						debuglog("lost a gem while researching dragon eggs at Heidi's Place.");
					}else{
						output("`n`nIt turns out that you forgot your mother's birthday.");
						addnews("%s forgot %s mother's birthday. Oh, that's so sad.",$session['user']['name'],translate_inline($session['user']['sex']>0?"her":"his"));
					}
				break;
			}
		break;
	}
	addnav("Return to Heidi's Place","runmodule.php?module=heidi");
	villagenav();
}
?>