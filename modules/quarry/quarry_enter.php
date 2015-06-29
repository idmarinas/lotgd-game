<?php
function quarry_enter(){
	$ruler=get_module_setting("ruler");
	$allprefs=unserialize(get_module_pref('allprefs'));
	if (get_module_setting("giantleft")<=0) {
		set_module_setting("underatk",0);
		debuglog("was the first to notice the giant siege was over.");
	}  
	if (get_module_setting("underatk")==1){
		if ($allprefs['sgfought']==0) {
			$allprefs['sgfought']=1;
			set_module_pref('allprefs',serialize($allprefs));
			output("`n`c`b`)S`&tone `)G`&iant `\$A`&ttack`c`b`n");
			output("`%A most desperate situation has occurred! `@T`3he `@Q`3uarry`% is under attack by a band of very angry `)S`&tone `)G`&iants`%!!  Before you have a chance to get away, you're dragged into combat.`n`n");
			output("Soon you find yourself engaged in a life or death battle with ");
			switch(e_rand(1,10)){
				case 1: case 2: case 3: case 4://small giant
					output("one of the `)S`&maller `)S`&tone `)G`&iants`%!");
					addnav("`)S`&tone `)G`&iant `\$A`&ttack","runmodule.php?module=quarry&op=smallgiant");
				break;
				case 5: case 6: case 7://medium giant
					output("one of the `)M`&edium `)S`&ized `)S`&tone `)G`&iants`%!");
					addnav("`)S`&tone `)G`&iant `\$A`&ttack","runmodule.php?module=quarry&op=medgiant");
				break;
				case 8: case 9://large giant
					output("a really `)B`&ig `)S`&tone `)G`&iant`% of the group!");
					addnav("`)S`&tone `)G`&iant `\$A`&ttack","runmodule.php?module=quarry&op=largegiant");
				break;
				case 10: //huge giant
					output("the `)B`&iggest `)B`&addest `)S`&tone `)G`&iant`% of the group!");
					addnav("`)S`&tone `)G`&iant `\$A`&ttack","runmodule.php?module=quarry&op=hugegiant");
				break;
			}
		}else{
			output("`n`c`b`)S`&tone `)G`&iant `\$A`&ttack`c`b`n");
			output("`%Before you get to `@T`3he `@Q`3uarry`%, you remember that it's under siege by `)S`&tone `)G`&iants`%.`n`nMaybe it will be safer to come back tomorrow.`n`n");
			addnav("V?(V) Return to Village","village.php");
		}
	}else{
		villagenav();
		if (is_module_active('lostruins') && get_module_setting("usequarry")==0) output("`n`c`b`@T`3he %s `@Q`3uarry`c`b`n",get_module_setting("quarryfinder"));
		else output("`n`c`b`@T`3he `@Q`3uarry`c`b`n");
		
		if ($allprefs['firstq']==0){
			$allprefs['firstq']=1;
			set_module_pref('allprefs',serialize($allprefs));
			if (is_module_active('lostruins') && get_module_setting("usequarry")==0) {
				output("`%You walk to the newly discovered quarry and see a dedication sign.  You lean in to read the beautiful inscription:`n`n");
				output("`^By ordinance of `&%s`^, this site is dedicated in honor of `b%s`b`^ for discovering the quarry.  May we all prosper because of the wonders inside!`n`n",$ruler,get_module_setting("quarryfinder"));
			}
			output("`%You wonder around a little to get a lay of the land.  It's clear that this is a very nice quarry full of the most wonderful stone; useful for solid construction.");
			output("You see two women looking at a large piece of paper.  One is wearing a blue hardhat, the other wearing a purple one.");
			output("They are pointing to different areas in the quarry then back at the blueprint.  Your curiosity gets the best of you and you wander over to them.`n`n");
			output("The woman in the blue hat looks up at you.  She is stunningly beautiful.  She has soft brown hair that falls to her shoulders and has amazing hazel eyes.");
			output("`n`n`^'Hello! My name is `&Slatemaker `\$S`7h`&y`\$l`7l`&e`^.  I am in charge of the quarry operation.  My friend here, `!Engineer `\$Uraal`^, is helping me plan our next cleave into the bedrock.'`n`n");
			output("`%Upon further examination, you realize `!Engineer `\$Uraal`% isn't wearing a  regular hardhat but rather is dressed in a purple hard-tophat.");
			output("Her lightheartedness is readily apparent in her demeanor and you realize instantly that you would gladly call her your friend. `n`n `@'If you're looking for some work, we could use your help.");
			output("I'll be honest though, it is definately no piece of pie, and it's a lot harder than hugging a tree.  But we've had some lucky workers lately that have left here very wealthy so far.'`n`n");
			output("`!Engineer `\$Uraal`% teaches you the basics of quarrying, including the use of the pick-axe and where your hard-hat will be stored.  After she feels comfortable with your ability, she looks at you expectantly.`n`n");
			output("`@'Are you ready to work?'`n`n");
			addnav("The Rules","runmodule.php?module=quarry&op=rules");
		}else{
			output("`%'Welcome back to the `@T`3he `@Q`3uarry`%. Are you ready to get some work done?'");
			if ($allprefs['usedqts']<get_module_setting("quarryturns")) addnav("Work the Quarry","runmodule.php?module=quarry&op=work");
			addnav("Review the Rules","runmodule.php?module=quarry&op=rules");
			addnav("Office","runmodule.php?module=quarry&op=office");
		}
	}
}
?>