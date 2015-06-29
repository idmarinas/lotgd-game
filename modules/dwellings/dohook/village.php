<?php
	tlschema($args['schemas']['gatenav']);
	addnav($args['gatenav']);
	tlschema();
	addnav(array("%s",translate_inline(get_module_setting("villagenav"))),"runmodule.php?module=dwellings");
	set_module_pref("dwelling_saver",0);
?>