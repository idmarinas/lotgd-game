<?php
	if(get_module_setting('usehof')==1){
		addnav("Warrior Rankings");
		addnav("Master Miners","runmodule.php?module=metalmine&op=metalhof");
	}
	if(get_module_setting('usehofr')==1){
		addnav("Warrior Rankings");
		addnav("Mine Rescuers","runmodule.php?module=metalmine&op=rescuehof");
	}
?>