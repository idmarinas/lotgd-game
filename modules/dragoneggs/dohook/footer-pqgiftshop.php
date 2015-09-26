<?php
	if ($session['user']['dragonkills']>=get_module_setting("mindk")){
		if ($op==""){
			addnav("Dragon Egg Research");
			if (get_module_setting("magiclodge")>0 && get_module_pref("magicaccess")==0) $lodge=1;
			else $lodge=0;
			if ($session['user']['dragonkills']<get_module_setting("magicmin")+get_module_setting("mindk")) $min=1;
			else $min=0;
			if ($lodge==1 && $min==1) $c="`Q";
			elseif ($lodge==1) $c="`!";
			elseif ($min==1) $c="`^";
			else $c="`@";
			if (get_module_setting("magicopen")==1) $c="`&";
			addnav(array("%sSearch %s's Ol' Gifte Shoppe",$c,get_module_setting('gsowner','pqgiftshop')),"runmodule.php?module=dragoneggs&op=magic");
		}
	}
?>