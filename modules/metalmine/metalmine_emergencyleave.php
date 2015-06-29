<?php
function metalmine_emergencyleave(){
	global $session;
	$op2 = httpget('op2');
	$allprefs=unserialize(get_module_pref('allprefs'));
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	if ($op2=="leavecanary"){
		output("You decide that you're not going to risk your life for some little bird.");
		if (is_module_active("alignment")){
			output("Yes, that is a bit of an `\$Evil`0 act, and your alignment suffers for it.`n`n");
			increment_module_pref("alignment",-3,"alignment");
		}
		output("You realize at this point that your canary will be killed in the accident. Please notice how the name of your canary has been wiped clean.`n`n");
		$allprefs['canary']="";
		set_module_pref('allprefs',serialize($allprefs));
	}
	output("You make a sprint to the exit and are greeted by many of the miners.  However, a quick scan of the faces of all the miners reveals something horrible has happened.");
	$chance=get_module_setting("rescue");
	output("`n`nThe supervisor counts all the miners and announces that there are `^%s miners`0 trapped in the mine.",$chance);
	if (get_module_setting("massyom")==1){
		$sql = "SELECT acctid FROM " . db_prefix("accounts");
		$result = db_query($sql);
		$name = $session['user']['name'];
		$staff= get_module_setting("frwhosend");
		require_once("lib/systemmail.php");
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$id = $row['acctid'];	
			$subj = array("`^Mine Collapse! Help!!");
			$body = array("`^A letter arrives from the Emergency Mining Commission:`n`n`0Dear Citizen of the Kingdom:`n`nThere has been a devastating accident in the mine caused by %s`0.  There are currently `^%s miners`0 trapped in the mine and in grave danger of dieing.  Please come to the mine in the forest to help rescue them.`n`nSincerely,`n`nThe Mining Commission",$session['user']['name'],$chance);
			systemmail($id,$subj,$body);
		}
	}
	set_module_setting("dayssince",0);
	set_module_setting("down",1);
	set_module_setting("accident",0);
	set_module_setting("effort",0);
	set_module_setting("whodoneit",$session['user']['name']);
	addnews("%s`0 caused the mine to collapse.  Please come to the mine to help rescue the trapped miners.",$session['user']['name']);
	addnav("Continue","runmodule.php?module=metalmine&op=emergencyleave2");
}
?>