<?php
	if ($session['user']['location'] == get_module_setting("orchardloc")){
		addnav($args["tavernnav"]);
		addnav("Fruit Orchard", "runmodule.php?module=orchard");
	}
?>