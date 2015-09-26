<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['seed']==16) addnav("Mention Seeds","runmodule.php?module=orchard&op=caseed");
?>