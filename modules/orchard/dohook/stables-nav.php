<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['seed']==7){
		if (getsetting("villagename", LOCATION_FIELDS) == $session['user']['location']){
			addnav("Other");
			addnav("Search for fruit seed", "runmodule.php?module=orchard&op=stables");
		}
	}
?>