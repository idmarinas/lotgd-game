<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	//reset the everything on newday if set for that
	if (!get_module_setting("runonce")) {
		$allprefs['usedqts']=0;
		$allprefs['sgfought']=0;
		$allprefs['insured']=0;
		$allprefs['stonesold']=0;
	}
	//if the quarry is undersiege this will announce it at newday
	if (get_module_setting("underatk")==1 && $allprefs['sgattack']==0){
		output("`n`@T`3he `@Q`3uarry`% is under siege by a band of `)S`&tone `)G`&iants`%!!!`n");
		$allprefs['sgattack']=1;
	}
	//if the quarry siege has ended this will announce it at newday
	if (get_module_setting("underatk")==0 && $allprefs['sgend']==0){
		output("`n`@T`3he `@Q`3uarry`% siege has ended.  Normal work in quarry can now resume.`n");
		$allprefs['sgend']=1;
	}
	set_module_pref('allprefs',serialize($allprefs));	
	if (is_module_active("lostruins") && get_module_setting('quarryfound','lostruins')==1 && get_module_setting("usequarry")==0) output("`n`@T`3he %s`& `@Q`3uarry `@o`3f `@G`3reat `@S`3tone `@was discovered in the village of %s.`n",get_module_setting("quarryfinder"),get_module_setting("quarryloc"));		
?>