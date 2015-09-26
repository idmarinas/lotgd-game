<?php
	if ($op=="" && get_module_pref("rumors")==6){
		addnav("Rumor");
		addnav("Gypsy Terror","runmodule.php?module=rumors&op=rumor6");
	}
?>