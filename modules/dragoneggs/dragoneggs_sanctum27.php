<?php
function dragoneggs_sanctum27(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Order of the Inner Sanctum");
	output("`n`c`b`&Order of the Inner Sanctum`b`c`7");
	if ($session['user']['specialty']=='Psychic'){
		$op2=round($op2/2);
		$chance=e_rand(1,3);
	}else{
		$chance=e_rand(1,6);
	}
	if ($chance==$op2){
		output("`&'Aha! I knew you had some psychic ability. Excellent.  Here's your `%gem`&,'`7 says the finely dressed man.");
		debuglog("gained a gem by predicting a number by researching at the Inner Sanctum.");
		$session['user']['gems']++;
	}else{
		output("`&'No, the number was `^%s`&,'`7 says the finely dressed man acting a bit disappointed.  Oh well.",$chance);
	}
	addnav("Return to the Order","runmodule.php?module=sanctum");
	villagenav();
}
?>