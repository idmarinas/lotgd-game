<?php
	if (get_module_setting("limitloc")==0 || (get_module_setting("limitloc")==1 && $session['user']['location'] == get_module_setting("oceanloc"))){
		if ($session['user']['dragonkills']>=get_module_setting("dockdks")){
			addnav("The Docks","runmodule.php?module=docks&op=docks&op2=enter");
		}
	}
?>