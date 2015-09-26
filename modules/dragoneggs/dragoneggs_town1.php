<?php
function dragoneggs_town1(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$header = color_sanitize($vname);
	page_header("%s Square",$header);
	output("`c`b`@%s Square`@`b`c",$vname);
	if ($op2==""){
		output("Having defeated the `\$Rthithc`@, you find a dragon egg.  You quickly cast an egg-destroying spell and save the Capital Town Square and `&gain a Dragon Egg Point`@!");
		increment_module_pref("dragoneggs",1,"dragoneggpoints");
		increment_module_pref("dragoneggshof",1,"dragoneggpoints");
		addnews("%s`@ destroyed a dragon egg that was found in the Capital Town Square.",$session['user']['name']);
		debuglog("destroyed a dragon egg in the Capital Town Square to gain a dragon egg point.");
	}elseif ($op2=="4"){
		output("You find yourself standing in the Capital Town Square next to a dragon egg.  You can easily destroy it now if you want to cast an egg-destroying spell.");
		output("It will cost you `%3 gems`@ to cast the spell.`n`nAre you ready to destroy it?");
		addnav("Destroy the Dragon Egg","runmodule.php?module=dragoneggs&op=town1&op2=destroy");
		addnav("Leave the Dragon Egg","runmodule.php?module=dragoneggs&op=town1&op2=leave");
		blocknav("village.php");
	}elseif ($op2=="destroy"){
		if ($session['user']['gems']>=3){
			output("You successfully destroy the egg! Congratulations.  You `&gain a Dragon Egg Point`@.");
			increment_module_pref("dragoneggs",1,"dragoneggpoints");
			increment_module_pref("dragoneggshof",1,"dragoneggpoints");
			$session['user']['gems']-=3;
			addnews("%s`@ destroyed a dragon egg in the Capital Town Square.",$session['user']['name']);
			debuglog("used 3 gems to destroy a dragon egg in the Capital Town Square and gain a dragon egg point.");
		}else{
			output("Oh, come on! You don't have `%3 gems`@.  What are you thinking?");
			if (get_module_setting("townegg")==0){
				output("`n`nYou end up leaving the egg in the Capital Town Square and hope someone else will be able to destroy it.");
				set_module_setting("townegg",1);
			}else output("Oh well. Maybe someone else will destroy it.");
			set_module_setting("deserter",$session['user']['name']);
			addnews("%s`@ left a dragon egg in the village unharmed.  Do you have the strength to destroy it?? It will be there for the rest of the day unless someone else destroys it first!",$session['user']['name']);
		}
	}elseif ($op2=="leave"){
		if (get_module_setting("townegg")==0){
			output("You end up leaving the egg in the Capital Town Square and hope someone else will be able to destroy it.");
			set_module_setting("townegg",1);
		}else output("Oh well. You leave the egg for someone else to deal with.");
		set_module_setting("deserter",$session['user']['name']);
		addnews("%s`@ left a dragon egg in the village unharmed.  Do you have the strength to destroy it?? It will be there for the rest of the day unless someone else destroys it first!",$session['user']['name']);
	}
	villagenav();
}
?>