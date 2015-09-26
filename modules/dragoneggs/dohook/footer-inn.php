<?php
	if ($session['user']['dragonkills']>=get_module_setting("mindk")){
		if ($op==""){
			if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
				$innname=getsetting("innname", LOCATION_INN);
			}else{
				$innname=translate_inline("The Boar's Head Inn");
			}
			addnav("Dragon Egg Research");
			if (get_module_setting("innlodge")>0 && get_module_pref("innaccess")==0) $lodge=1;
			else $lodge=0;
			if ($session['user']['dragonkills']<get_module_setting("innmin")+get_module_setting("mindk")) $min=1;
			else $min=0;
			if ($lodge==1 && $min==1) $c="`Q";
			elseif ($lodge==1) $c="`!";
			elseif ($min==1) $c="`^";
			else $c="`@";
			if (get_module_setting("innopen")==1) $c="`&";
			addnav(array("%sSearch %s",$c,$innname),"runmodule.php?module=dragoneggs&op=inn");
		}
	}
?>