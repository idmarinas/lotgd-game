<?php
function dragoneggs_weapons13(){
	global $session;
	page_header("MightyE's Weapons");
	output("`c`b`&MightyE's Weapons`b`c`7");
	$value=round($session['user']['weaponvalue']*.75);
	if ($value>=1000){
		output("You hand over your weapon and take the new weapon in hand.  Good deal! You feel `^Tough`7!");
	}elseif ($value+$session['user']['gold']>=10000){
		$pay=10000-$value;
		output("You hand over your weapon and `^%s gold`7 and take the new weapon in hand.  The balance is nice, and you feel `^Tough`7.",$pay);
		$session['user']['gold']-=$pay;
		debuglog("paid $pay gold and their old weapon to get a Ice Sword (attack 17) and Tough Title while researching dragon eggs at the Weapons Store.");
	}else{
		$pay=10000-$session['user']['gold']-$value;
		output("You hand over your weapon, all your money, and sign a bank slip to withdraw `^%s gold`7 to pay off the shopkeepper.",$pay);
		output("`n`nYou pick up the `#Ice Sword`7 and feel `^Tough`7!");
		$session['user']['gold']=0;
		$session['user']['goldinbank']-=$pay;
		debuglog("paid all money on hand, $pay money from the bank, and their old weapon to get a Ice Soword (attack 17) and Tough Title while researching dragon eggs at the Weapons Store.");
	}
	require_once("lib/names.php");
	$newtitle = translate_inline("Tough");
	$newname = change_player_title($newtitle);
	$session['user']['title'] = $newtitle;
	$session['user']['name'] = $newname;
	$session['user']['weaponvalue']=10000;
	$session['user']['attack']-=$session['user']['weapondmg'];
	$session['user']['attack']+=17;
	$session['user']['weapondmg']=17;
	$session['user']['weapon']="`#Ice Sword`0";
	addnav("Return to MightyE's Weapons","weapons.php");
	villagenav();
}
?>