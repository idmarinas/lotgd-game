<?php
	if ($session['user']['dragonkills']>=get_module_setting("mindk")){
		if (is_module_active("library")==0 && is_module_active("dlibrary")){
			if ($op=="enter"){
				addnav("Dragon Egg Research");
				if (get_module_setting("librarylodge")>0 && get_module_pref("libraryaccess")==0) $lodge=1;
				else $lodge=0;
				if ($session['user']['dragonkills']<get_module_setting("librarymin")+get_module_setting("mindk")) $min=1;
				else $min=0;
				if ($lodge==1 && $min==1) $c="`Q";
				elseif ($lodge==1) $c="`!";
				elseif ($min==1) $c="`^";
				else $c="`@";
				if (get_module_setting("libraryopen")==1) $c="`&";
				addnav(array("%sSearch the Library",$c),"runmodule.php?module=dragoneggs&op=library");
			}
		}
	}
?>