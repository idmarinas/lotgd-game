<?php
	if ($session['user']['dragonkills']>=get_module_setting("mindk")){
		if ($op=="" && $session['user']['hitpoints']>0){
			addnav("Dragon Egg Research");
			if (get_module_setting("newslodge")>0 && get_module_pref("newsaccess")==0) $lodge=1;
			else $lodge=0;
			if ($session['user']['dragonkills']<get_module_setting("newsmin")+get_module_setting("mindk")) $min=1;
			else $min=0;
			if ($lodge==1 && $min==1) $c="`Q";
			elseif ($lodge==1) $c="`!";
			elseif ($min==1) $c="`^";
			else $c="`@";
			if (get_module_setting("newsopen")==1) $c="`&";
			addnav(array("%sSearch the Daily News",$c),"runmodule.php?module=dragoneggs&op=news");
			if (get_module_pref("quest2")==3){
				addnav("Gift Shop Quest");
				addnav("Deliver Clue","runmodule.php?module=dragoneggs&op=magic15&op2=3");
			}
		}
	}
?>