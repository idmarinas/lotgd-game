<?php
	if ($op=="" && get_module_pref("rumors")==1){
		addnav("Rumor");
		addnav("Donate Blood","runmodule.php?module=rumors&op=rumor1");
	}
?>