<?php
	//modified slightly from backpack module by Webpixie
	global $session;
	if($session['user']['dragonkills']>=get_module_setting("mindk")){
		
		$left=get_module_setting("research")-get_module_pref("researches");
		if (!strstr($SCRIPT_NAME, "village")){
			$info=$left;
		}else{
			$vc=translate_inline(" `&[`^ Info `&]");
			//$info="<a href='runmodule.php?module=dragoneggs&op=explain' onClick=\"".popup("runmodule.php?module=dragoneggs&op=explain").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">"."`^".$left.$vc."</a>";
			$info="<a href='runmodule.php?module=dragoneggs&op=explain'\">`^".$left.$vc."</a>";
			addnav("","runmodule.php?module=dragoneggs&op=explain");
		}
	
		if (get_module_pref("retainer")==1) $ret=translate_inline("Yes");
		elseif (get_module_pref("retainer")==2) $ret=translate_inline("Pending");
		else $ret=translate_inline("None");
		if (!strstr($SCRIPT_NAME, "village")){
			$info3=$ret;
		}else{
			$ret.=translate_inline(" `&[`^ Info `&]");
			//$info3="<a href='runmodule.php?module=dragoneggs&op=retainer' onClick=\"".popup("runmodule.php?module=dragoneggs&op=retainer").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">"."`^".$ret."</a>";
			$info3="<a href='runmodule.php?module=dragoneggs&op=retainer'\">`^".$ret."</a>";
			//output("`^<a href=\"runmodule.php?module=oceanquest&op=docks&op2=hints\">.$ret.</a>",true);
			addnav("","runmodule.php?module=dragoneggs&op=retainer");
		}
		addcharstat("Personal Info");
		addcharstat("Dragon Egg Searches",$info);
		addcharstat("Retainer",$info3);
	}
?>