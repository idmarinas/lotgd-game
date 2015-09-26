<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['seed']==19) addnav(array("Star Fruit Seed (%s points)", get_module_setting("lodgeseed")),"runmodule.php?module=orchard&op=starseedbuy");
?>