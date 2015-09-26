<?php
	if (get_module_pref("rumors")==9 && get_module_pref("progress")>0){
		if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
			global $options;
			$check=$options['type'];
		}else{
			$check=$args['type'];
		}
		if (($args['creatureid']==get_module_setting("level1") || 
			$args['creatureid']==get_module_setting("level2") ||
			$args['creatureid']==get_module_setting("level3") ||
			$args['creatureid']==get_module_setting("level4") ||
			$args['creatureid']==get_module_setting("level5") ||
			$args['creatureid']==get_module_setting("level6") ||
			$args['creatureid']==get_module_setting("level7") ||
			$args['creatureid']==get_module_setting("level8") ||
			$args['creatureid']==get_module_setting("level9") ||
			$args['creatureid']==get_module_setting("level10") ||
			$args['creatureid']==get_module_setting("level11") ||
			$args['creatureid']==get_module_setting("level12") ||
			$args['creatureid']==get_module_setting("level13") ||
			$args['creatureid']==get_module_setting("level14") ||
			$args['creatureid']==get_module_setting("level15") ||
			$args['creatureid']==get_module_setting("level16"))
			&&  $check== 'forest'){
			output("`n`c`b`@`#Dragon Sympathizers `^Kill`b`c`n");
			output("`\$You have killed a Dragon Sympathizer.  You collect the Talisman around the Dragon Sympathizer's neck.  Congratulations!");
			increment_module_pref("progress",-1);
			if (get_module_pref("progress")==0){
				output("`n`nYou have defeated enough Dragon Sympathizers to dispel the rumor. Return all the Talismans you've collected to put it to an end.`n`n");
			}else{
				output("`n`nYou only need to defeat `^%s`\$ more Dragon Sympathizers in order to dispel the rumor.`n`n",get_module_pref("progress"));
			}
		}
	}
?>