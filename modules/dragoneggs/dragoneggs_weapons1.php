<?php
function dragoneggs_weapons1(){
	global $session;
	page_header("MightyE's Weapons");
	output("`c`b`&MightyE's Weapons`b`c`7");
	$session['user']['gold']-=250;
	$chance=e_rand(1,5);
	$level=$session['user']['level'];
	output("You hand over the money and collect the trinket.`n`n");
	if (($level>7 && $chance>2) || ($level<=7 && $chance>3)){
		output("As you pick up the Letter Opener, you feel a strange glow spread through your hands.");
		output("`n`nYou've improved in your specialty!");
		require_once("lib/increment_specialty.php");
		increment_specialty("`!");
		debuglog("incremented specialty for 250 gold by researching at the MightyE's Weapons.");
	}else{
		output("You pick it up, hoping that some magically wonderful spell overcomes you. ");
		if ($session['user']['weapon']=="`!Letter Opener`0"){
			output("`n`nNothing happens.");
		}else{
			output("Instead, you find yourself holding an ordinary Letter Opener; and you decide to use it instead of your current weapon.");
			$session['user']['weapon']="`!Letter Opener`0";
		}
		debuglog("spent 250 gold for a letter opener by researching at the MightyE's Weapons.");
	}
	addnav("Return to MightyE's Weapons","weapons.php");
	villagenav();
}
?>