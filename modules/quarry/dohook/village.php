<?php
	if (is_module_active('lostruins') && get_module_setting("usequarry")==0) {
		if(get_module_setting("quarryfound","lostruins")==1){
			if ($session['user']['location'] == get_module_setting("quarryloc")){
				tlschema($args['schemas']['gatenav']);
				addnav($args['gatenav']);
				tlschema();
				addnav("The Quarry","runmodule.php?module=quarry&op=enter");
			}
		}
	}else{
		if ($session['user']['location'] == get_module_setting("quarryloc")) {
			tlschema($args['schemas']['gatenav']);
			addnav($args['gatenav']);
			tlschema();
			addnav("The Quarry","runmodule.php?module=quarry&op=enter");
		}
	}
?>