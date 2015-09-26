<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['seed']==6){
		addnav("Other");
		addnav("Ask Odin about Seed", "runmodule.php?module=orchard&op=odin");
	}
?>