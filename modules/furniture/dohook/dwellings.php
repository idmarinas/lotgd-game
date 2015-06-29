<?php
	if ($session['user']['dragonkills']>=get_module_setting("dkenter")){
		addnav("Dwellings Stores");
		addnav(array("%s",get_module_setting("storename")),"runmodule.php?module=furniture&op=enter&loc=store");
	}
?>