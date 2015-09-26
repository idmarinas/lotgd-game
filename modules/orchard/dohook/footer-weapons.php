<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['seed']==14) addnav("Ask About Avocados","runmodule.php?module=orchard&op=megame");
?>