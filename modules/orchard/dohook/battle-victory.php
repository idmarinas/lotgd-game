<?php
	global $options;
	$allprefs=unserialize(get_module_pref('allprefs'));
	if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
		global $options;
		$check=$options['type'];
	}else{
		$check=$args['type'];
	}
	if ($allprefs['monsterid']==$args['creatureid'] && $check == 'forest' && $allprefs['monsterid']>0){
		output("`nYou find a small seed on the slain monsters corpse.  It appears to be exactly the seed you were looking for!");
		$allprefs['monsterid']="";
		$allprefs['monsterlevel']="";
		$allprefs['monstername']="";
		set_module_pref('allprefs',serialize($allprefs));
		require_once("modules/orchard/orchard_func.php");
		orchard_findseed();
	}
?>