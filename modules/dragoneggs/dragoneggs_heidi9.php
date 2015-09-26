<?php
function dragoneggs_heidi9(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Heidi's Place");
	output("`c`b`&Heidi, the Well-wisher`b`c`7`n");
	if ($op2==""){
		if (get_module_pref("puzzlepiece")!=2) $session['user']['turns']--;
		$chance=e_rand(1,13);
		if (get_module_pref("puzzlepiece")==2 || (($session['user']['level']>10 && $chance<4) || ($session['user']['level']<11 && $chance<3))){
			if (get_module_pref("puzzlepiece")==2) output("You pull out the puzzle piece that you found in the forest and get to work.");
			output("You sit down to study the puzzle and work on it diligently.");
			output("`n`nAfter a while, you finish the puzzle and notice that it isn't just a picture of a bottle, it's an actual bottle!");
			output("`n`nWill you take the bottle and drink it?");
			addnav("Drink the Bottle","runmodule.php?module=dragoneggs&op=heidi9&op2=yes");
		}else{
			output("You sit down to study the puzzle and almost complete it, but there's a piece missing.");
			output("You decide you will keep your eyes out for the missing puzzle piece. If you can find it before you kill your next `@Green Dragon`7 you'll probably be able to finish the puzzle.");
			set_module_pref("puzzlepiece",1);
		}
	}else{
		output("You grab a hold of the bottle and drink it down.");
		output("`n`nYou feel invigorated! You `@gain a permanent hitpoint`7 and are `@healed to full`7!!");
		$session['user']['maxhitpoints']++;
		$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		set_module_pref("puzzlepiece",0);
		debuglog("finished a puzzle and gained a permanent hitpoint and healed to full while researching dragon eggs at Heidi's Place.");
	}
	addnav("Return to Heidi's Place","runmodule.php?module=heidi");
	villagenav();
}
?>