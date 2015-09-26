<?php
function dragoneggs_armor7(){
	global $session;
	page_header("Pegasus Armor");
	output("`c`b`%Pegasus Armor`b`c`5");
	output("Sunglasses are very cool so you accept the offer.  You put them on and your armor improves by 1.");
	output("`n`nAs soon as you put them on, you feel them pinching your nose.  Oh Curses! No wonder `#Pegasus's`5 been having such a bad time.  These `)Sunglasses`5 are `iCursed`i!!");
	$session['user']['armor']="`)Sunglasses `^& ".$session['user']['armor'];
	$session['user']['armordef']++;
	$session['user']['defense']++;
	if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
		$session['bufflist']['blesscurse']['rounds'] += 5;
		debuglog("increased armor by 1 with sunglasses but increased their curse by 5 rounds while researching dragon eggs at Pegasus Armor.");
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
		debuglog("increased armor by 1 with sunglasses but was cursed while researching dragon eggs at Pegasus Armor.");
	}
	addnav("Return to Pegasus Armor","armor.php");
	villagenav();
}
?>