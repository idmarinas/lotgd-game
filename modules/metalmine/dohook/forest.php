<?php
	if (get_module_setting("limitloc")==0 || (get_module_setting("limitloc")==1 && $session['user']['location'] == get_module_setting("metalloc1"))){
		if (get_module_setting("ffs")>0) addnav("Go to the Mine","runmodule.php?module=metalmine");
		else addnav("To the Mine","runmodule.php?module=metalmine&op=enter");
	}
	if (get_module_setting("limitloc")==2){
		for ($i=1;$i<=3;$i++) {
			if ($session['user']['location'] == get_module_setting("metalloc".$i)){
				$entrance=get_module_setting("name".$i);
				if (get_module_setting("ffs")>0) addnav(array("Go to Mine via %s`0 Entrance", $entrance),"runmodule.php?module=metalmine&op3=$i");
				else addnav(array("To the Mine via %s`0 Entrance", $entrance),"runmodule.php?module=metalmine&op=enter&op3=$i");
			}
		}
	}
?>