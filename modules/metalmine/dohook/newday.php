<?php
	if (get_module_setting("down")==1){
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['since']=$allprefs['since']+1;
		output("`n`0There are still miners trapped in the mine.  Will you help them today?`n");
		set_module_pref('allprefs',serialize($allprefs));
	}
	if (!get_module_setting("runonce")){
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['usedmts']=0;
		$allprefs['drinkstoday']=0;
		$allprefs['found']=0;
		$allprefs['metalsold']=0;
		set_module_pref('allprefs',serialize($allprefs));
	}
?>