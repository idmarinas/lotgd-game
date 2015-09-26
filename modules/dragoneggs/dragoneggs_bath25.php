<?php
function dragoneggs_bath25(){
	global $session;
	page_header("The Outhouse");
	output("`n`c`b`2The Outhouse`b`c`2`n");
	if ($op2=="no"){
		output("Who wants adventure anyway?");
		addnav("Return to the Outhouse","runmodule.php?module=outhouse");
		addnav("Return to the Forest","forest.php");
	}else{
		output("Well, this sounds like a good idea!");
		$dks=$session['user']['dragonkills'];
		if(get_module_setting("weaponsopen")==1 ||($dks>=get_module_setting("weaponsmin")+get_module_setting("mindk") && ((get_module_setting("weaponslodge")>0 && get_module_pref("weaponsaccess")>0) || get_module_setting("weaponslodge")==0))) addnav("Secret Passageway","runmodule.php?module=dragoneggs&op=weapons");
		else addnav("Secret Passageway","runmodule.php?module=dragoneggs&op=town");
		if (get_module_pref("researches")>=2) increment_module_pref("researches",-2);
		else increment_module_pref("researches",-1);
		debuglog("gained 1-2 research turns by leaving to research at the weapons shop or Capital Town Square while researching dragon eggs at the Outhouse.");
	}
}
?>