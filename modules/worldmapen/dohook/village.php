<?php
// blocknav("runmodule.php?module=cities&op=travel");

// if (get_module_setting("showforestnav")==0) 
	// blocknav("forest.php");

addnav($args["gatenav"]);
addnav("Journey","runmodule.php?module=worldmapen&op=beginjourney");
?>