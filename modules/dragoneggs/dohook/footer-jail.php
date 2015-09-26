<?php
	if ($session['user']['dragonkills']>=get_module_setting("mindk")){
		if ($op=="" && get_module_pref("injail","jail")==0){
			addnav("Dragon Egg Research");
			if (get_module_setting("policelodge")>0 && get_module_pref("policeaccess")==0) $lodge=1;
			else $lodge=0;
			if ($session['user']['dragonkills']<get_module_setting("policemin")+get_module_setting("mindk")) $min=1;
			else $min=0;
			if ($lodge==1 && $min==1) $c="`Q";
			elseif ($lodge==1) $c="`!";
			elseif ($min==1) $c="`^";
			else $c="`@";
			if (get_module_setting("policeopen")==1) $c="`&";
			addnav(array("%sSearch the Jail",$c),"runmodule.php?module=dragoneggs&op=police");
			if (get_module_pref("quest1")==1 && get_module_pref("injail","jail")==0) increment_module_pref("quest1",1);
		}
	}
?>