<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	$stat=get_module_pref("user_stat");
	if ($allprefs['squares']>0) $squares=$allprefs['squares'];
	else $squares=0;
	if ($squares>0){ 
		if ($stat==1) setcharstat("Personal Info", "Wood", "`^$squares");
		elseif ($stat==2) setcharstat("Materials","Wood", "`^$squares");
	}
?>