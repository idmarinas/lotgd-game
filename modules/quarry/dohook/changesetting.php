<?php
	if ($args['setting'] == "villagename") {
		if ($args['old'] == get_module_setting("quarryloc")) set_module_setting("quarryloc", $args['new']);
	}
?>