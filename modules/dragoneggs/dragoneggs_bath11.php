<?php
function dragoneggs_bath11(){
	global $session;
	page_header("The Outhouse");
	output("`n`c`b`2The Outhouse`b`c`2`n");
	$gems=e_rand(1,3);
	if ($gems==3 && e_rand(1,3)<3) $gems=2;
	$session['user']['gold']-=400;
	$session['user']['gems']+=$gems;
	output("You pay the money and buy the book.  You read through it quickly and discover a way of identifying gems.  You look around and find `%%s %s`2.",$gems,translate_inline($gems>1?"gems":"gem"));
	debuglog("lost 400 gold and gained $gems gems while researching dragon eggs at the Outhouse.");
	addnav("Return to the Outhouse","runmodule.php?module=outhouse");
	addnav("Return to the Forest","forest.php");
}
?>