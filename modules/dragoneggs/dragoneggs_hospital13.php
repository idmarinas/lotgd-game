<?php
function dragoneggs_hospital13(){
	page_header("Healer's Hut");
	output("`c`b`#Healer's Hut`b`c`3`nYou decide to stay at the Healer's Hut.");
	increment_module_pref("researches",1);
	require_once("lib/forest.php");
	forest(true);
}
?>