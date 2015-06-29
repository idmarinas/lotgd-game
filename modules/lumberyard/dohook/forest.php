<?php
global $session;
	$allowed=0;
	if (is_module_active("cityprefs") && get_module_setting("limitloc")==2){
		require_once("modules/cityprefs/lib.php");
		$loc=get_cityprefs_cityid("location",$session['user']['location']);
		$allowed= get_module_objpref("city",$loc,"chophere","lumberyard");
	}elseif(is_module_active("cities") && get_module_setting("limitloc")==1){
		if (get_module_setting("limitloc")==0 || (get_module_setting("limitloc")==1 && $session['user']['location'] == get_module_setting("lumberloc"))) $allowed=1;
	}elseif(is_module_active("cities")==0 && is_module_active("cityprefs")==0) $allowed=1;
	if ($allowed==1){
		addnav("Lumber Yard","runmodule.php?module=lumberyard&op=enter");
	}
?>