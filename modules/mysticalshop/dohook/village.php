<?php
if (get_module_pref("weapon") == 1) blocknav("weapons.php");
if (get_module_pref("armor") == 1) blocknav("armor.php");
$shop = get_module_setting("shopname");
addnav($args['marketnav']);		
if ($session['user']['location'] == get_module_setting("shoploc") && get_module_setting("shopall") == 0){
	if (get_module_pref("pass") == 1 && get_module_setting("shopappear") == 1){
		addnav("$shop",$from."op=shop&what=enter");
	}else if (get_module_setting("shopappear") == 0 && $session['user']['dragonkills']>=get_module_setting("dkreq")){									
		addnav("$shop",$from."op=shop&what=enter");
	}
}else if (get_module_setting("shopall") == 1){
	if (get_module_pref("pass") == 1 && get_module_setting("shopappear") == 1){
			addnav("$shop",$from."op=shop&what=enter");
	}else if (get_module_setting("shopappear") == 0
		&& $session['user']['dragonkills']>=get_module_setting("dkreq")){									
			addnav("$shop",$from."op=shop&what=enter");
	}
}
?>