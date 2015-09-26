<?php
	if ($op=="" && get_module_pref("rumors")==4){
		addnav("Rumor");
		addnav("Abandoned Animals","runmodule.php?module=rumors&op=rumor4");
	}
?>