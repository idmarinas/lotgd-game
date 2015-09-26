<?php
function dragoneggs_bank25(){
	page_header("Ye Olde Bank");
	output("`c`b`^Ye Olde Bank`b`c`6You decide you want to finish your work here at the bank.");
	increment_module_pref("researches",1);
	addnav("Continue Banking","bank.php");
	villagenav();
}
?>