<?php
function dragoneggs_animal21(){
	global $session;
	page_header("Merick's Stables");
	output("`c`b`^Merick's Stables`b`c`7`n");
	$turns=e_rand(1,2);
	output("A drink from the pool invigorates you! You gain `@%s %s`7!",$turns,translate_inline($turns>1?"turns":"turn"));
	$session['user']['turns']+=$turns;
	debuglog("gained $turns turns researching dragon eggs at Merick's Stables.");
	villagenav();
}
?>