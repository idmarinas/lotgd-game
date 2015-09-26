<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['seed']==18) addnav("Ask About Cranberries","runmodule.php?module=orchard&op=pegcran");
?>