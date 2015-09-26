<?php
	//modified from backpack module by Webpixie
	global $session,$SCRIPT_NAME;
	if($session['user']['dragonkills']>=get_module_setting("mindk","dragoneggs")){
		$rumors=get_module_pref("rumors");
		$array=array("","Blood Shortage","Failed Recruiting","Bank Solvency","Abandoned Animals","Impending Invasion","End of the World","Outhouse Dragon Egg","Vampire Capture","Dragon Sympathizer Rise");
		if (!strstr($SCRIPT_NAME, "village")){
			if ($rumors>0) $info2=$array[$rumors];
			else $info2=translate_inline("None");
		}else{
			if ($rumors>0) $ab=$array[$rumors]." `&[`^ Info `&]";
			else $ab=translate_inline("None `&[`^ Info `&]");
			//$info2="<a href='runmodule.php?module=rumors&op=rumors' onClick=\"".popup("runmodule.php?module=rumors&op=rumors").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">"."`^".$ab."</a>";
			$info2="<a href='runmodule.php?module=rumors&op=rumors'\">"."`^".$ab."</a>";
			addnav("","runmodule.php?module=rumors&op=rumors");
		}
		addcharstat("Personal Info");
		addcharstat("Rumors",$info2);
	}
?>