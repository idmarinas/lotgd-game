<?
if (is_module_active("jail")==0){
	if ($args['setting'] == "villagename")
		if ($args['old'] == get_module_setting('jailloc')) set_module_setting('jailloc', $args['new']);
}
?>