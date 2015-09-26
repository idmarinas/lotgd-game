<?php
	if ($session['user']['dragonkills']>=get_module_setting("mindk")){
		if($op==""){
			addnav("Dragon Egg Research");
			if (get_module_setting("armorlodge")>0 && get_module_pref("armoraccess")==0) $lodge=1;
			else $lodge=0;
			if ($session['user']['dragonkills']<get_module_setting("armormin")+get_module_setting("mindk")) $min=1;
			else $min=0;
			if ($lodge==1 && $min==1) $c="`Q";
			elseif ($lodge==1) $c="`!";
			elseif ($min==1) $c="`^";
			else $c="`@";
			if (get_module_setting("armoropen")==1) $c="`&";
			addnav(array("%sSearch Pegasus Armor",$c),"runmodule.php?module=dragoneggs&op=armor");
			if (get_module_pref("lantern")>0){
				output("`n`n`#Pegasus`5 also mentions that she would be interested in any antiques if you ever find any.");
				addnav("Antiques");
				addnav("Sell Antique Lantern","runmodule.php?module=dragoneggs&op=animal13");
			}
		}
	}
?>