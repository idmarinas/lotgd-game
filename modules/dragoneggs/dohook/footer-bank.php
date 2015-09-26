<?php
	if ($session['user']['dragonkills']>=get_module_setting("mindk")){
		if($op==""){
			addnav("Dragon Egg Research");
			if (get_module_setting("banklodge")>0 && get_module_pref("bankaccess")==0) $lodge=1;
			else $lodge=0;
			if ($session['user']['dragonkills']<get_module_setting("bankmin")+get_module_setting("mindk")) $min=1;
			else $min=0;
			if ($lodge==1 && $min==1) $c="`Q";
			elseif ($lodge==1) $c="`!";
			elseif ($min==1) $c="`^";
			else $c="`@";
			if (get_module_setting("bankopen")==1) $c="`&";
			addnav(array("%sSearch Ye Olde Bank",$c),"runmodule.php?module=dragoneggs&op=bank");
			if (get_module_pref("quest2")==2){
				addnav("Gift Shop Quest");
				addnav("Deliver Gem","runmodule.php?module=dragoneggs&op=magic15&op2=2");
			}
		}
	}
?>