<?php
	if ($args['setting'] == "villagename") {
		if ($args['old'] == get_module_setting("orchardloc")) set_module_setting("orchardloc", $args['new']);
	}
?>