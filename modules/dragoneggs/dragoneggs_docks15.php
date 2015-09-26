<?php
function dragoneggs_docks15(){
	global $session;
	page_header("The Docks");
	output("`c`b`^The Docks`b`c`7`n");
	if (is_module_active("oceanquest")) $docks="oceanquest";
	else $docks="docks";
	$session['user']['turns']-=3;
	$session['user']['gems']-=3;
	output("You carefully pick up the strange creature and a tentacle grabs onto your armor.  You `@spend 3 turns`7 trying to get free and finally do.");
	output("`n`nThen you get desperate and throw `%3 gems`7 at it. Suddenly, you realize that the tentacle is giving your armor extra strength.  It's amazing!");
	output("`n`nYour armor becomes `iReinforced`i and `&improves by 2`7.  In addition, your `&defense increases by 1`7!");
	output("`n`nUnfortunately, the whole experience makes you a little sick to your stomach.");
	if (isset($session['bufflist']['stomachache'])) {
		$session['bufflist']['stomachache']['rounds'] += 5;
	}else{
		apply_buff('stomachache',
			array("name"=>translate_inline("Upset Stomach"),
				"rounds"=>10,
				"wearoff"=>translate_inline("Your stomach feels better."),
				"minioncount"=>1,
				"effectmsg"=>translate_inline("`5Your stomach stabs you for `^{damage} hitpoints`5 and you feel ill."),
				"effectnodmgmsg"=>translate_inline("Your stomach doesn't hurt right now."),
				"effectfailmsg"=>translate_inline("Your stomach doesn't hurt right now."),
				"mingoodguydamage"=>0,
				"maxgoodguydamage"=>ceil($session['user']['level']/3),
			)
		);
	}
	$session['user']['armor']="Reinforced ".$session['user']['armor'];
	$session['user']['armordef']+=2;
	$session['user']['defense']+=3;
	$session['user']['armorvalue']*=1.3;
	debuglog("spent 3 turns and 3 gems to Reinforce their armor to increase armordef by 2, increase defense by 1, and increase value of armor by 1.3x while researching dragon eggs at the Docks.");
	addnav("Return to the Docks","runmodule.php?module=$docks&op=docks&op2=enter");
	addnav("Return to the Forest","forest.php");
}
?>