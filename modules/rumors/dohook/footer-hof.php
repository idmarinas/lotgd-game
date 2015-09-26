<?php
	addnav("Warrior Rankings");
	addnav("Rumor Dispellers","runmodule.php?module=rumors&op=hof");	
	if ($op=="" && get_module_pref("rumors")==5){
		addnav("Rumor");
		addnav("Document Research","runmodule.php?module=rumors&op=rumor5");
	}
?>