<?php
	if ($op=="" && get_module_pref("rumors")==2){
		addnav("Rumor");
		addnav("Detective Recruiting","runmodule.php?module=rumors&op=rumor2");
	}
?>