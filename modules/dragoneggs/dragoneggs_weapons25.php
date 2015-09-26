<?php
function dragoneggs_weapons25(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("MightyE's Weapons");
	output("`c`b`&MightyE's Weapons`b`c`7");
	output("Deciding that you know a good deal when you hear one, you go through your gems to give to `!MightyE`7.");
	output("`n`nTrying to be fair, you give him some of your better gems.`n`n");
	if ($op2==1){
		output("He gives you `^300 gold`7 for the `%gem`7.");
		$session['user']['gems']--;
		$session['user']['gold']+=300;
		debuglog("spent 1 gem to gain 300 gold by researching at the MightyE's Weapons.");
	}elseif ($op2==2){
		output("He gives you `^900 gold`7 for the `%2 gems`7.");
		$session['user']['gems']-=2;
		$session['user']['gold']+=900;
		debuglog("spent 2 gems to gain 900 gold by researching at the MightyE's Weapons.");
	}else{
		output("He gives you `^1500 gold`7 for the `%3 gems`7.");
		$session['user']['gems']-=3;
		$session['user']['gold']+=1500;
		debuglog("spent 3 gems to gain 1500 gold by researching at the MightyE's Weapons.");
	}
	addnav("Return to MightyE's Weapons","weapons.php");
	villagenav();
}
?>