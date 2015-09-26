<?php
	module_addhook("changesetting");
	module_addhook("village");
	module_addhook("newday");
	module_addhook("newday-runonce");
	module_addhook("ale");
	module_addhook("stables-nav");
	module_addhook("dragonkilltext");
	module_addhook("battle-victory");
	module_addhook("mausoleum");
	module_addhook("ancienthall");
	module_addhook("footer-crazyaudrey"); //added by DaveS
	module_addhook("lodge"); 		//added by DaveS
	module_addhook("footer-armor"); // added by DaveS
	module_addhook("footer-weapons"); // added by DaveS
	module_addhook("footer-bank"); 	// added by DaveS
	module_addhook("injail");		// added by DaveS
	module_addhook("footer-hof"); 	// added by Billie Kennedy
	module_addhook("charstats");  	// added by Billie Kennedy
	module_addhook("allprefs");
	module_addhook("allprefnavs");
	module_addeventhook("forest",
		"require_once(\"modules/orchard.php\");
		return orchard_percent(\"forest\");");
	if (is_module_active("darkalley")) {
		module_addhook("darkalley");
		module_addeventhook("darkalley",
			"require_once(\"modules/orchard.php\");
			return orchard_percent(\"darkalley\");");
	}
	if (is_module_active("cellar")) {
		module_addeventhook("cellar",
			"require_once(\"modules/orchard.php\");
			return orchard_percent(\"cellar\");");
	}
	return true;
?>