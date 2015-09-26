<?php
function dragoneggs_church11(){
	global $session;
	page_header("Old Church");
	$session['user']['gems']-=2;
	$expbonus=$session['user']['dragonkills']*e_rand(4,6);
	$expgain =$session['user']['level']*e_rand(45,55)+$expbonus;
	$session['user']['experience']+=$expgain;
	output("You spend some quality time exchanging information with `5Capelthwaite`3.  It turns out to be a very useful session.");
	output("`n`nYou exchange `%2 gems`3 for `#%s experience`3.",$expgain);
	debuglog("gained $expgain experience for 2 gems while researching dragon eggs at the Church.");
	addnav("Return to the Church","runmodule.php?module=oldchurch");
	villagenav();
}
?>