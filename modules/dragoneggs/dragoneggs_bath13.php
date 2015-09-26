<?php
function dragoneggs_bath13(){
	global $session;
	page_header("The Outhouse");
	output("`n`c`b`2The Outhouse`b`c`2`n");
	if (e_rand(1,6)<6){
		output("It's a tasty cracker!");
		$gain=e_rand(10,60);
		output("`n`nYou `\$gain %s temporary hitpoints`2.",$gain);
		$session['user']['hitpoints']+=$gain;
		debuglog("gained $gain temporary hitpoints while researching dragon eggs at the Outhouse.");
	}else{
		output("Errr... you picked up a cracker... in an outhouse... and ATE it????");
		output("`n`nSee, that's just nasty.  You lose a `&charm point`2 because you're grossing me out.");
		$session['user']['charm']--;
		debuglog("lost a charm while researching dragon eggs at the Outhouse.");
		addnews("%s`^ was at the outhouse and saw a cracker.  Guess what happened next... Yup... %s ATE AN OUTHOUSE CRACKER!!!",$session['user']['name'],translate_inline($session['user']['sex']?"SHE":"HE"));
	}
	addnav("Return to the Outhouse","runmodule.php?module=outhouse");
	addnav("Return to the Forest","forest.php");
}
?>