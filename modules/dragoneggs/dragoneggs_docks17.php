<?php
function dragoneggs_docks17(){
	global $session;
	page_header("The Docks");
	output("`c`b`^The Docks`b`c`7`n");
	if (is_module_active("oceanquest")) $docks="oceanquest";
	else $docks="docks";
	output("You agree to take the item off his hands and he smiles.  He gives you `^100 gold`7, tells you that's all he has and he's sorry. The next thing you know is that he's putting a large slimey leech in your hand and running away.");
	output("`n`nYou can't shake it off and you feel just horrible.  You realize it's an `\$EVIL Leech`7 and you are `iCursed`i!");
	$session['user']['gold']+=100;
	if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
		$session['bufflist']['blesscurse']['rounds'] += 5;
		debuglog("increased their curse by 5 rounds and got 100 gold and Leech Title while researching dragon eggs at the Docks.");
	}else{
		apply_buff('blesscurse',
			array("name"=>translate_inline("Cursed"),
				"rounds"=>15,
				"survivenewday"=>1,
				"wearoff"=>translate_inline("The burst of energy passes."),
				"atkmod"=>0.8,
				"defmod"=>0.9,
				"roundmsg"=>translate_inline("Dark Energy flows through you!"),
			)
		);
		debuglog("was cursed and got 100 gold and Leech Title while researching dragon eggs at the Docks.");
	}
	require_once("lib/names.php");
	$newtitle = translate_inline("Leech");
	$newname = change_player_title($newtitle);
	$session['user']['title'] = $newtitle;
	$session['user']['name'] = $newname;
	addnav("Return to the Docks","runmodule.php?module=$docks&op=docks&op2=enter");
	addnav("Return to the Forest","forest.php");
}
?>