<?php
if ($args['setting'] == "villagename")
	if ($args['old'] == get_module_setting('jailloc')) set_module_setting('jailloc', $args['new']);
?>