<?php
function dragoneggs_rock13(){
	global $session;
	page_header("The Veteran's Club");
	output("`c`b`2The Veteran's Club`b`c`4`n`n");
	$first= strpos($session['user']['weapon'],"Sheldon Sword")!==false ? 1 : 0;
	$second= strpos($session['user']['weapon'],"Sheldon Halberd")!==false ? 1 : 0;
	$chance=e_rand(1,9);
	$level=$session['user']['level'];
	if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3) && ($first==0 || $second==0)){
		if ($first==0) $session['user']['weapon']="Sheldon Sword";
		else $session['user']['weapon']="Sheldon Halberd";
		output("You grab the %s and leave your old weapon behind. You pin a little note on the Gang Members shirt that says `#'Thanks for the trade'`4 and off you go.",$session['user']['weapon']);
		addnav("Return to the Curious Looking Rock","rock.php");
		villagenav();
	}else{
		output("You grab the weapon and... uh oh! He wakes up. He's not very happy with you. You're in for a fight and remember... he's got the better weapon!");
		addnav("Attack the Sheldon Boy","runmodule.php?module=dragoneggs&op=attack");
		set_module_pref("monster",13);
	}
}
?>