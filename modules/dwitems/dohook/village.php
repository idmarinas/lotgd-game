<?php
if(get_module_setting("location","dwitems") == $session['user']['location'] && $session['user']['dragonkills'] >= get_module_setting("mindk","dwitems")){
	tlschema($args['schemas']['marketnav']);
	addnav($args['marketnav']);
	tlschema();
	addnav("Maeher's Household Supply", "runmodule.php?module=dwitems&op=shop");
}
?>