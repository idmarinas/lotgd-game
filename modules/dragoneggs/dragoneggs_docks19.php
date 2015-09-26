<?php
function dragoneggs_docks19(){
	global $session;
	page_header("The Docks");
	output("`c`b`^The Docks`b`c`7`n");
	output("You fox! Stealing a box of socks on the docks!");
	if (is_module_active("oceanquest")) $docks="oceanquest";
	else $docks="docks";
	if (isset($session['bufflist']['docksock'])) {
		$session['bufflist']['docksock']['rounds'] += 5;
		output("`n`nOooh, these are nice socks. You find 5 more socks and they will make a wonderful weapon.");
	}else{
		apply_buff('docksock',array(
			"name"=>translate_inline("Socks from the Docks"),
			"rounds"=>10,
			"survivenewday"=>1,
			"wearoff"=>translate_inline("You're all out of socks."),
			"minioncount"=>1,
			"effectmsg"=>translate_inline("`2You throw the sock at {badguy}`2 and cause confusion. {badguy}`2 stumbles and loses `^{damage}`2 hitpoints."),
			"effectnodmgmsg"=>translate_inline("Your sock misses."),
			"effectfailmsg"=>translate_inline("Your sock misses."),
			"minbadguydamage"=>0,
			"maxbadguydamage"=>ceil($session['user']['level']/2)+1,
		));
		output("`n`nOooh, these are nice socks. You find 5 pairs and they will make a wonderful weapon.");
	}
	$level=$session['user']['level'];
	$chance=e_rand(1,5);
	if (is_module_active("jail")||is_module_active("djail")) $jail=1;
	else $jail=0;
	if ($jail==0 || (($level>8 && $chance<=3) || ($level<=8 && $chance<=2))){
		addnav("Return to the Docks","runmodule.php?module=$docks&op=docks&op2=enter");
		addnav("Return to the Forest","forest.php");
		debuglog("received the docksock buff while researching dragon eggs on the docks.");
	}else{
		output("`n`nUnfortunately, these were not socks for the public to take.  A Police Officer sees you with your socks and arrests you for Theft.");
		output("`n`n`@'Off to jail you go,'`7 he says. Luckily you get to keep your socks!");
		if (is_module_active("jail")) $jail="jail";
		else $jail="djail";
		set_module_pref("injail",1,$jail);
		addnav("To Jail", "runmodule.php?module=$jail");
		debuglog("received the docksock buff butt went to jail while researching dragon eggs on the docks.");
	}
}
?>