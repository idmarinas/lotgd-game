<?php
	if ($args['setting'] == "villagename") {
		if ($args['old'] == get_module_setting("furnitureshoploc")) set_module_setting("furnitureshoploc", $args['new']);
	}
?>