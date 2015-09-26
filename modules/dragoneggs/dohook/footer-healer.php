<?php
	if ($session['user']['dragonkills']>=get_module_setting("mindk")){
		if ($op==""){
			addnav("Dragon Egg Research");
			if (get_module_setting("heallodge")>0 && get_module_pref("healaccess")==0) $lodge=1;
			else $lodge=0;
			if ($session['user']['dragonkills']<get_module_setting("healmin")+get_module_setting("mindk")) $min=1;
			else $min=0;
			if ($lodge==1 && $min==1) $c="`Q";
			elseif ($lodge==1) $c="`!";
			elseif ($min==1) $c="`^";
			else $c="`@";
			if (get_module_setting("healopen")==1) $c="`&";
			addnav(array("%sSearch the Healer's Hut",$c),"runmodule.php?module=dragoneggs&op=hospital");	
			if (get_module_pref("quest2")==1){
				addnav("Gift Shop Quest");
				addnav("Deliver Gem","runmodule.php?module=dragoneggs&op=magic15&op2=1");
			}
			if (get_module_pref("quest1")==4) increment_module_pref("quest1",1);
		}
	}
?>