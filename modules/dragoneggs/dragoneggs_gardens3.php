<?php
function dragoneggs_gardens3(){
	global $session;
	page_header("The Gardens");
	output("`c`b`2The Gardens`b`c`7`n`n");
	output("You see a pair of Dragon Sympathists trying to cast a Dragon Egg creation spell.  You jump up and yell `#'Gooogledy Goggledy!!!'`7");
	output("`n`nThey get scared and run off, dropping something shiny! You `%gain a gem`7!");
	$session['user']['gems']++;
	debuglog("gained a gem researching in the Gardens.");
	addnav("Return to the Gardens","gardens.php");
	villagenav();
}
?>