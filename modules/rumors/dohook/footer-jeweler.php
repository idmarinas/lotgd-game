<?php
	if ($op=="" && get_module_pref("rumors")==9){
		addnav("Rumor");
		addnav("Rising Dragon Sympathizers","runmodule.php?module=rumors&op=rumor9");
	}
?>