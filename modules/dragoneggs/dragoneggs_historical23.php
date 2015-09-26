<?php
function dragoneggs_historical23(){
	global $session;
	page_header("Hall of Fame");
	output("`c`b`^The Hall of Fame`b`c`@`n");
	$level=$session['user']['level'];
	$chance=e_rand(1,9);
	if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
		output("You work the pages apart with great success!");
		output("`n`nYou excitedly read the pages and find yourself reading a magic incantation!");
		output("`n`nYou `bgain 1 permanent hitpoint`b!");
		$session['user']['maxhitpoints']++;
		$session['user']['hitpoints']++;
		debuglog("gained 1 max hitpoint while researching dragon eggs at the Hall of Fame.");
	}else{
		output("You spend a turn trying to pull the pages apart but you fail.");
		$session['user']['turns']--;
		debuglog("lost a turn while researching dragon eggs at the Hall of Fame.");
	}
	addnav("Return to Hall of Fame","hof.php");
	villagenav();
}
?>