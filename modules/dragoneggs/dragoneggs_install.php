<?php
	if (is_module_active("bakery")) module_addhook("footer-bakery");
	if (is_module_active("heidi")) module_addhook("footer-heidi");
	if (is_module_active("oldchurch")) module_addhook("footer-oldchurch");
	if (is_module_active("oldhouse")) module_addhook("footer-oldhouse");
	if (is_module_active("outhouse")) module_addhook("footer-outhouse");
	if (is_module_active("petra")) module_addhook("footer-petra");
	if (is_module_active("pqgiftshop")) module_addhook("footer-pqgiftshop");
	if (is_module_active("jeweler")) module_addhook("footer-jeweler");

	if (is_module_active("jail")) module_addhook("footer-jail");
	elseif (is_module_active("djail")) module_addhook("footer-djail");
	if (is_module_active("oceanquest")) module_addhook("footer-oceanquest");
	elseif (is_module_active("docks")) module_addhook("footer-docks");
	if (is_module_active("library")) module_addhook("footer-library");
	elseif (is_module_active("dlibrary")) module_addhook("footer-dlibrary");

	if (is_module_active("sanctum")) module_addhook("footer-sanctum");
	
	module_addhook("footer-armor");
	module_addhook("footer-bank");
	module_addhook("footer-gardens");
	module_addhook("footer-gypsy");
	module_addhook("footer-healer");
	module_addhook("footer-hof");
	module_addhook("footer-inn");
	module_addhook("footer-news");
	module_addhook("footer-rock");
	module_addhook("footer-stables");
	module_addhook("footer-train");
	module_addhook("footer-weapons");
	module_addhook("village");
	
	module_addhook("charstats");
	module_addhook("dragonkill");
	module_addhook("lodge");
	module_addhook("newday");
	module_addhook("newday-runonce");
	module_addhook("pointsdesc");
	
	module_addeventhook("forest","require_once(\"modules/dragoneggs.php\"); 
	return dragoneggs_chance();");
	return true;
?>