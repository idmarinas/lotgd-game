<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['seed']==12) addnav("Order Last Meal","runmodule.php?module=orchard&op=lastmeal");
?>