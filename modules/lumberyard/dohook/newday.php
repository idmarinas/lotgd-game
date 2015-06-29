<?php
	if (!get_module_setting("runonce")) {
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['usedlts']=0;
		$allprefs['squaresold']=0;
		set_module_pref('allprefs',serialize($allprefs));	
	}
	if (get_module_setting("remainsize")==0) output("`@`nThere`6 aren't any trees");
	elseif (get_module_setting("remainsize")==1) output("`@`nThere `6is one tree");
	else output("`n`@There are `6%s trees",get_module_setting("remainsize"));
	output("`@ in the forest that can be harvested in `b`QT`qhe `QL`qumber `QY`qard`b`@.`n");
	if (get_module_setting("cutdown")==0){
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['ccspiel']=0;
		set_module_pref('allprefs',serialize($allprefs));	
	}
?>