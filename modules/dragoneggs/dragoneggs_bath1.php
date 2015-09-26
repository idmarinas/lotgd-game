<?php
function dragoneggs_bath1(){
	global $session;
	page_header("The Outhouse");
	output("`n`c`b`2The Outhouse`b`c`2`n");
	$session['user']['turns']-=2;
	if (e_rand(1,4)<4){
		output("`@Two turns later`2 you feel happy.  This is some great tea! You see someting shiny in the bottom of the cup. You've `%gained a gem`2.");
		output("`n`nEven better, you find that you've `\$gained a permanent hitpoint`2 from the tea!");
		$session['user']['maxhitpoints']++;
		$session['user']['gems']++;
		debuglog("gained a gem and a permanent hitpoint by spending 2 turns while researching dragon eggs at the Outhouse.");
	}else{
		output("You `@spend 2 turns`2 drinking tea. How boring.");
		debuglog("spent 2 turns drinking tea while researching dragon eggs at the Outhouse.");
	}
	addnav("Return to the Outhouse","runmodule.php?module=outhouse");
	addnav("Return to the Forest","forest.php");
}
?>