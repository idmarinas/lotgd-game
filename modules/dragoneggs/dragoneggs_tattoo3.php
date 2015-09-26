<?php
function dragoneggs_tattoo3(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Tattoo Parlor");
	output("`c`b`&Petra, the Ink Artist`b`c`7");
	if ($session['user']['gold']<$op3){
		output("Not having enough money on hand, you make a quick run to the bank to get enough for the transaction.");
		if ($session['user']['gold']<1){
			$session['user']['goldinbank']-=$op3;
			output("You hand over the `^%s gold`7 from your bank account.",$op3);
			$debug="paid $op3 gold from their bank account";
		}else{
			$inbank=$op3-$session['user']['gold'];
			output("You hand over all your money on hand and `^%s gold`7 from your bank account.",$inbank);
			$session['user']['goldinbank']-=$inbank;
			$gold=$session['user']['gold'];
			$session['user']['gold']=0;
			$debug="paid $inbank gold from their bank account and $gold gold from onhand";
		}
	}else{
		$session['user']['gold']-=$op3;
		output("You hand over the `^%s gold`7 from your cash-on-hand.",$op3);
		$debug="paid $op3 gold from onhand";
	}
	output_notl("`n`n");
	if ($op2==3){
		output("You pick up the new weapon and realize it's a quality crossbow.");
		$session['user']['attack']-=$session['user']['weapondmg'];
		$session['user']['weapondmg']=17;
		$session['user']['attack']+=17;
		$session['user']['weaponvalue']=12000;
		$session['user']['weapon']="`#Silver Tipped Crossbow`0";
		debuglog("bought a Silver Tipped Crossbow (def 17) and ".$debug." while researching dragon eggs at the Tattoo Parlor.");
	}elseif ($op2==2){
		output("You try on the new armor and like the fit.");
		$session['user']['defense']-=$session['user']['armordef'];
		$session['user']['armordef']=16;
		$session['user']['defense']+=16;
		$session['user']['armorvalue']=11000;
		$session['user']['armor']="`#Reinforced Overcoat`0";
		debuglog("bought a Reinforced Overcoat armor (def 16) and ".$debug." while researching dragon eggs at the Tattoo Parlor.");
	}elseif ($op2==1){
		if (isset($session['bufflist']['throwingstar'])) {
			output("You decide that you like those throwing stars so you buy another 20.");
			$session['bufflist']['throwingstar']['rounds'] += 20;
			debuglog("bought 20 more throwing stars and ".$debug." while researching dragon eggs at the Tattoo Parlor.");
		}else{
			output("You take the throwing stars and realize they're so lightweight you'll be able to use them instantly without any penalty.");
			apply_buff('throwingstar',array(
				"name"=>translate_inline("Throwing Stars"),
				"rounds"=>20,
				"survivenewday"=>1,
				"wearoff"=>translate_inline("You run out of throwing stars."),
				"minioncount"=>1,
				"effectmsg"=>"`#Your Throwing Star pierces {badguy}`# for `^{damage}`# damage.",
				"effectnodmgmsg"=>"`#Your Throwing Star misses!",
				"minbadguydamage"=>0,
				"maxbadguydamage"=>10+$session['user']['level'],
			));
			debuglog("bought 20 throwing stars and ".$debug." while researching dragon eggs at the Tattoo Parlor.");
		}
	}
	addnav("Return to the Tattoo Parlor","runmodule.php?module=petra");
	villagenav();
}
?>