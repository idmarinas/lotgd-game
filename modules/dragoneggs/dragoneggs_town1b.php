<?php
function dragoneggs_town1b(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$header = color_sanitize($vname);
	page_header("%s Square",$header);
	output("`c`b`@%s Square`@`b`c",$vname);
	if ($op2==""){
		output("You discover the egg that %s`@ was unable to destroy.`n`n",get_module_setting("deserter"));
		if ($session['user']['dragonkills']>=get_module_setting("mindk")){
			output("If you spend `%6 gems`@ you can cast a spell to destroy the egg.  Will you do it??");
			addnav("Destroy the Dragon Egg","runmodule.php?module=dragoneggs&op=town1b&op2=destroy");
			addnav("Leave the Dragon Egg","runmodule.php?module=dragoneggs&op=town1b&op2=leave");
			blocknav("village.php");
		}else output("Unfortunately, you don't have the ability to destroy the egg at this time.");
	}elseif ($op2=="destroy"){
		if ($session['user']['gems']>=6){
			if (get_module_setting("townegg")==1){
				output("You successfully destroy the egg! Congratulations.  You `&gain a Dragon Egg Point`@.");
				increment_module_pref("dragoneggs",1,"dragoneggpoints");
				increment_module_pref("dragoneggshof",1,"dragoneggpoints");
				$session['user']['gems']-=6;
				set_module_setting("townegg",0);
				set_module_setting("deserter","");
				addnews("%s`@ destroyed the deserted dragon egg in %s Square`@.",$session['user']['name'],$vname);
				debuglog("used 6 gems to destroy a dragon egg in the Capital Town Square and gain a dragon egg point.");
			}else{
				output("Well, it looks like someone destroyed the egg while you were thinking about it.  Better luck next time!");
			}
		}else{
			output("Oh, come on! You don't have `%6 gems`@.  What are you thinking?`n`n");
			if (get_module_setting("townegg")==0){
				output("Luckily, it looks like someone else took care of it while you were thinking about it.");
			}else{
				output("Oh well. Maybe someone else will destroy it.");
				if (get_module_setting("deserter")!=$session['user']['name']){
					set_module_setting("deserter",$session['user']['name']);
					addnews("%s`@ left a dragon egg in %s Square`@ unharmed.  Do you have the strength to destroy it?? It will be there for the rest of the day unless someone else destroys it first!",$session['user']['name'],$vname);
				}
			}
		}	
	}elseif ($op2=="leave"){
		if (get_module_setting("townegg")==0){
			output("Luckily, it looks like someone else took care of it while you were getting ready to leave.");
		}else{
			output("Oh well. Maybe someone else will destroy it.");
			if (get_module_setting("deserter")!=$session['user']['name']){
				set_module_setting("deserter",$session['user']['name']);
				addnews("%s`@ left a dragon egg in %s Square`@ unharmed.  Do you have the strength to destroy it?? It will be there for the rest of the day unless someone else destroys it first!",$session['user']['name'],$vname);
			}
		}
	}
	villagenav();
}
?>