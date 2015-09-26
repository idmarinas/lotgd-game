<?php	
	if ($session['user']['dragonkills']>=get_module_setting("mindk")&&$session['user']['location']==getsetting("villagename", LOCATION_FIELDS)){
		addnav("Dragon Egg Research");
		if (get_module_setting("allopen")>=1) $c="`&";
		else $c="`@";
		addnav(array("%sSearch %s Square",$c,getsetting("villagename", LOCATION_FIELDS)),"runmodule.php?module=dragoneggs&op=town");
		if (get_module_setting("left")>0) {
			tlschema($args['schemas']['marketnav']);
			addnav($args['marketnav']);
			tlschema();
			$name=get_module_setting("found");
			addnav(array("%s's Gem Exchange",$name),"runmodule.php?module=dragoneggs&op=exchange");
		}
		if (get_module_setting("townegg")==1){
			addnav("Dragon Egg Research");
			addnav("Destroy Deserted Egg","runmodule.php?module=dragoneggs&op=town1b");
		}
	}
?>