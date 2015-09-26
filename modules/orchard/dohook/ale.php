<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['seed']==4){
		addnav("Other");
		addnav("Ask about fruit seed", "runmodule.php?module=orchard&op=cedrick");
	}
?>