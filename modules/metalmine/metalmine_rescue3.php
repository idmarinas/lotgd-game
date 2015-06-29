<?php
function metalmine_rescue3(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	output("`n`c`b`&Metal `)Mine`0`c`b`n");
	output("You feel very tired after all this work.  You have used all your `^mining turns`0 for the day working to rescue the poor trapped miners.");
	increment_module_setting("effort",1);
	$mineturnset=get_module_setting("mineturnset");
	$marray=translate_inline(array("","`)Iron Ore`0","`QCopper`0","`&Mithril`0"));
	$allprefs['help']=1;
	$allprefs['rescuehof']++;
	$allprefs['usedmts']=$mineturnset;
	set_module_pref('allprefs',serialize($allprefs));
	if (get_module_setting("effort")<get_module_setting("rescue")){
		addnav("Continue","runmodule.php?module=metalmine&op=enter");
		output("`n`nYou head back to the surface with some of the other workers.  All of you have the glow of optimism and of a job well done.");
		apply_buff('metalmine',array(
			"name"=>"`&Rescue High",
			"rounds"=>5,
			"atkmod"=>1.05,
			"roundmsg"=>"Helping others makes you fight better.",
		));
	}elseif (get_module_setting("effort")>=get_module_setting("rescue")){
		addnav("Continue","runmodule.php?module=metalmine&op=rescue4");
		output("You can't leave though... you know you're so close!");
		output("`n`nYou fight back fatigue and use all your strength to break through to the trapped miners...");
		output("`n`nAnd you succeed!!!");
		output("`n`nYou've freed the trapped miners! They are all very grateful and a collection is made for you.");
		$metal=e_rand(1,3);
		$grams=round(10000/$mineturnset);
		output("You are given `^%s grams`0 of %s`0. It has turned out to be a great day!.`n`n",$grams,$marray[$metal]);
		apply_buff('metalmine',array(
			"name"=>"`&Rescue High",
			"rounds"=>10,
			"atkmod"=>1.1,
			"roundmsg"=>"Helping others makes you fight better.",
		));
		addnews("`^Thanks to the heroic efforts of many citizens, the mine collapse caused by %s`^ that trapped several miners has been cleared.  There have been no fatalities.",get_module_setting("whodoneit"));
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
		$allprefs['metalhof']=$allprefs['metalhof']+$grams;
		set_module_pref('allprefs',serialize($allprefs));
		set_module_setting("down",0);
		set_module_setting("effort",0);
		set_module_setting("accident",0);
		set_module_setting("whodoneit",0);
		//Only penalize people if the mass yom has been enabled
		if (get_module_setting("massyom")==1){
			require_once("lib/systemmail.php");
			$sql = "SELECT acctid,name FROM ".db_prefix("accounts")."";
			$result = db_query($sql);
			for ($i=0;$i<db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				$id = $row['acctid'];
				$name=$row['name'];
				$subj = translate_inline("`^Miners Rescued");
				$allprefsmail=unserialize(get_module_pref('allprefs','metalmine',$id));
				//Only penalize players that have been to the mine
				if ($allprefsmail['firstm']==1){
					if ($allprefsmail['help']==1) $body= array("`^Dear Citizen:`n`nThanks to your heroic efforts, the miners that were trapped in the mine have been rescued.  The mine has been re-opened. Safety measures have been implemented to prevent any further accidents.`n`nSincerely,`n`nThe Mine Safety Commission");
					elseif ($allprefsmail['help']==0){
						if ($allprefsmail['since']>=1){
							if (is_module_active("alignment") && get_module_setting("losealign")==1) {
								//users that continually logged on and didn't help get a greater alignment penalty; max 10 loss
								$loss=ceil($allprefs['since']/3);
								if ($loss>10) $loss=10;
								increment_module_pref("alignment",-$loss,"alignment",$id);
								$body = array("`^Dear Citizen:`n`nIt has been noted that despite the call for help to the mine you did not show up to help rescue the trapped miners even though you were seen in the kingdom.  Although we understand that you have priorities, helping others is considered a cornerstone of our kingdom.  Due to your failure to respond, your alignment has been penalized to reflect your `\$Evil`^ nature.`n`nSincerely,`n`nThe Ethics Board");
							}else $body = array("`^Dear Citizen:`n`nIt has been noted that despite the call for help to the mine you did not show up to help rescue the trapped miners even though you were seen in the kingdom.  Although we understand that you have priorities, helping others is considered a cornerstone of our kingdom. Please try to help next time.`n`nSincerely,`n`nThe Ethics Board");
							addnews("%s`% did not help rescue the trapped miners despite being available for the rescue effort.",$name);
						}else $body = array("`^Dear Citizen:`n`nIn your absence, the miners that were trapped were rescued.  Please disregard the call for help.`n`nSincerely,`n`nThe Mine Safety Commission");
					}
					systemmail($id,$subj,$body);
				}
			}
		}
	}
}
?>