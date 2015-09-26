<?php
	if ($op=="" && get_module_pref("rumors")==8){
		addnav("Rumor");
		addnav("Vampire's Tale","runmodule.php?module=rumors&op=rumor8");
	}
?>