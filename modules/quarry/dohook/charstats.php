<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	$stat=get_module_pref("user_stat");
	$blocks=$allprefs['blocks'];
	if ($blocks>0){ 
		if ($stat==1) setcharstat("Personal Info", "Stone", "`^$blocks");
		elseif ($stat==2) setcharstat("Materials","Stone", "`^$blocks");
	}
?>