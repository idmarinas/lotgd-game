<?php
function dragoneggs_tattoo19(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Tattoo Parlor");
	output("`c`b`&Petra, the Ink Artist`b`c`7");
	$chance=e_rand(1,9);
	$level=$session['user']['level'];
	$session['user']['gold']-=100;
	output("You put down the `^100 gold`7 and pick up the dart.`n`n");
	if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
		output("You throw a dart and hit the bullseye...  Nicely done! You collect the pot... `^300 gold`7!!");
		$session['user']['gold']+=300;
		debuglog("won 200 gold playing cards while researching dragon eggs at the Tattoo Parlor.");
	}else{
		output("You throw the dart and miss the board completely!");
		if ($session['user']['hitpoints']<$session['user']['maxhiptoints']){
			output("The impish man feels bad about your pitiful throw and gives you a healing elixir, healing you back to full!");
			$session['user']['hitpoints']=$session['user']['maxhiptoints'];
			debuglog("lost 100 gold and had hitpoints fill to full while researching dragon eggs at the Tattoo Parlor.");
		}else{
			output("You leave disappointed, but planning to practice a little more for the next chance you get to play.");
			debuglog("lost 100 gold playing darts while researching dragon eggs at the Tattoo Parlor.");
		}
	}
	addnav("Return to the Tattoo Parlor","runmodule.php?module=petra");
	villagenav();
}
?>