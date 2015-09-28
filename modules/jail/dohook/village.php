<?php
if (get_module_pref('injail')) redirect("runmodule.php?module=jail");
if (get_module_setting('oneloc'))
{
	if ($session['user']['location'] == get_module_setting('jailloc'))
	{
		tlschema($args['schemas']['marketnav']);
		addnav($args['marketnav']);
		tlschema();
		addnav("The Jail", "runmodule.php?module=jail");
	}
}    
else
{ 
	addnav($args['marketnav']);
	addnav(array("%s Jail",$session['user']['location']), "runmodule.php?module=jail");
}
?>