<?php
	if(get_module_setting('usehof')){
		addnav("Warrior Rankings");
		addnav("Lumberjacks", "runmodule.php?module=lumberyard&op=squareshof");	
	}
	if(get_module_setting('usehofp')){
		addnav("Warrior Rankings");
		addnav("Arborists", "runmodule.php?module=lumberyard&op=planthof");	
	}
?>