<?php
function dragoneggs_tattoo9(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Tattoo Parlor");
	output("`c`b`&Petra, the Ink Artist`b`c`7");
	$chance=e_rand(1,5);
	if (($session['user']['level']>6 && $chance<4) || ($session['user']['level']<6 && $chance<3)){
		output("You play a masterful hand and find that you've won.  You collect the pot... `^500 gold`7!!");
		$session['user']['gold']+=500;
		debuglog("won 500 gold playing cards while researching dragon eggs at the Tattoo Parlor.");
	}else{
		output("You find yourself holding a whole lotta nothing.");
		if ($session['user']['gold']<300){
			output("Uh Oh... it looks like you don't have `^300 gold`7... this is bad.`n`nThe guys at the table rough you up a bit.");
			blocknav("runmodule.php?module=petra");
			output("You're thrown out of the tattoo parlor after getting beat up pretty bad and having all the money you have stolen from you.");
			$session['user']['hitpoints']=1;
			$gold=$session['user']['gold'];
			$session['user']['gold']=0;
			debuglog("lost $gold gold and all hitpoints except 1 playing cards while researching dragon eggs at the Tattoo Parlor.");
		}else{
			output("You hand over the `^300 gold`7 and wonder if the game was rigged.");
			$session['user']['gold']-=300;
			debuglog("lost 300 gold playing cards while researching dragon eggs at the Tattoo Parlor.");
		}
	}
	addnav("Return to the Tattoo Parlor","runmodule.php?module=petra");
	villagenav();
}
?>