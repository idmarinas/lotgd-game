<?php
	if(get_module_setting('usehof')==1){
		addnav("Warrior Rankings");
		addnav("Master Masons","runmodule.php?module=quarry&op=blockshof");
	}
	if(get_module_setting('usehofgiants')==1){
		addnav("Warrior Rankings");
		addnav("Giant Killers","runmodule.php?module=quarry&op=gianthof");
	}
?>