<?php
	if ($op=="" && get_module_pref("rumors")==3){
		addnav("Rumor");
		addnav("Bank Solvency","runmodule.php?module=rumors&op=rumor3");
	}
?>