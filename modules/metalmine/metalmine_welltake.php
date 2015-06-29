<?php
function metalmine_welltake(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	output("You gather up `^67 gold`0.");
	$session['user']['gold']+=67;
	$chance=e_rand(1,5);
	if (is_module_active('alignment')) increment_module_pref("alignment",-1,"alignment");
	if ($chance==1){
		output("Suddenly, the `%'Under the Well Troll'`0 sees you stealing all it's money!");
		output("`n`n`%'GARRRGGGGHHHHH!!!!!!'`0 it screams.");
		output("`n`nHaving an understanding of `%'Under the Well Troll Speak'`0 you realize he just said:");
		output("`n`n`%'Kind sir, I believe that your actions are intrusive and uninspiring.  In fact, I believe that you are violating the sacred trust of the faithful and pure of heart.");
		output("Due to your inappropriate activities, I challenge you to a duel.'");
		addnav("Fight `%Under the Well Troll","runmodule.php?module=metalmine&op=welltroll");
	}else{
		$mineturnset=get_module_setting("mineturnset");
		$usedmts=$allprefs['usedmts'];
		$mineturns=$mineturnset-$usedmts;
		if ($mineturns>0) output("`n`nYou have `^%s Mine %s`0 left.",$mineturns,translate_inline($mineturns>1?"Turns":"Turn"));
		elseif ($session['user']['hitpoints']>0) output("`n`nYou've used up all your `^Mine Turns`0 for the day. It's probably time for you to head out.");
		addnav("Metal Mine");
		if ($usedmts<$mineturnset){
			addnav("Work The Mine More","runmodule.php?module=metalmine&op=work");
			if (get_module_setting("limitloc")<=1) addnav("Travel To a Different Area","runmodule.php?module=metalmine&op=travel");
		}
		addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
	}
}
?>