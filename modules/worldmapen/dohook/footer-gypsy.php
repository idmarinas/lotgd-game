<?php
if (get_module_setting("worldmapAcquire") == 1 && $op==""){
	addnav("Map");
	addnav("Ask about World Map",
	"runmodule.php?module=worldmapen&op=gypsy");
}
?>