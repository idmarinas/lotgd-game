<?php
	if ($session['user']['dragonkills']>=get_module_setting("mindk")){
		if ($op=="docks" && $op2=="enter"){
			addnav("Dragon Egg Research");
			if (get_module_setting("dockslodge")>0 && get_module_pref("docksaccess")==0) $lodge=1;
			else $lodge=0;
			if ($session['user']['dragonkills']<get_module_setting("docksmin")+get_module_setting("mindk")) $min=1;
			else $min=0;
			if ($lodge==1 && $min==1) $c="`Q";
			elseif ($lodge==1) $c="`!";
			elseif ($min==1) $c="`^";
			else $c="`@";
			if (get_module_setting("docksopen")==1) $c="`&";
			addnav(array("%sSearch the Docks",$c),"runmodule.php?module=dragoneggs&op=docks");
		}
	}
?>