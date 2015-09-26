<?php
	if (get_module_setting("hof")==1){
		addnav("Warrior Rankings");
		addnav("Largest Fish Ever Caught","runmodule.php?module=docks&op=docks&op2=bigfish&op3=hof");
		addnav("Most Fish by Weight","runmodule.php?module=docks&op=docks&op2=fishweight&op3=hof");
		addnav("Most Fish by Number","runmodule.php?module=docks&op=docks&op2=numberfish&op3=hof");
	}
?>