<?
if (is_module_active("jail")==0){
	if (get_module_pref('injail')) redirect("runmodule.php?module=djail");
	if ((get_module_setting('oneloc')&& $session['user']['location'] == get_module_setting('jailloc'))|| get_module_setting("oneloc")==0){
		tlschema($args['schemas']['marketnav']);
		addnav($args['marketnav']);
		tlschema();
		addnav(array("%s Jail",$session['user']['location']), "runmodule.php?module=djail");
	}
}
?>