<?php
	if ($op=="" && get_module_pref("rumors")==7){
		addnav("Rumor");
		addnav("Expanding Gate","runmodule.php?module=rumors&op=rumor7");
	}
?>