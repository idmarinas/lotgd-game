<?php
function dragoneggs_train9(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Bluspring's Warrior Training");
	output("`c`b`7Bluspring's Warrior Training`b`c");
	if ($op2==1){
		output("You hand over the gold and have a training session.  You gain an attack!");
		$session['user']['attack']++;
		debuglog("gained an attack for 500 gold by researching at Bluspring's Warrior Training.");
	}else{
		$session['user']['defense']++;
		output("You hand over the gold and have a training session.  You gain an defense!");
		debuglog("gained an defense for 500 gold by researching at Bluspring's Warrior Training.");
	}
	$session['user']['gold']-=500;
	addnav("Continue at the Bluspring's Warrior Training","train.php");
	villagenav();
}
?>