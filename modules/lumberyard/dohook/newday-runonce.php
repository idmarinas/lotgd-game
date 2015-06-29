<?php
	//increase the forest size by the daily growth if the forest isn't quite full yet
	if (get_module_setting("remainsize")<get_module_setting("fullsize")-get_module_setting("daygrowth")) set_module_setting("remainsize",get_module_setting("remainsize")+get_module_setting("daygrowth"));
	
	//set the wood price for the day
	$squarepay=(e_rand(get_module_setting("squarepaymin"),get_module_setting("squarepaymax")));
	set_module_setting("squarepay",$squarepay);
	
	//calculate the chance for a counter to trigger and if it triggers, increment the count if the forest isn't cutdown
	$cutchance=(e_rand(1,100));
	if ($cutchance<=get_module_setting("cutpercent") && get_module_setting("cutdown")==0) increment_module_setting("cccount",1);
	//if the counter is greater than the clearcut number, set the size to zero, the counter to zero, and the forest as cut down
	if (get_module_setting("cccount")>=get_module_setting("clearcut")) {
		set_module_setting("remainsize",0);
		set_module_setting("cutdown",1);
		set_module_setting("cccount",0);
	}
	//if the forest has naturally grown bigger than the need for planting then turn off the cutdown
	if (get_module_setting("cutdown")==1 && get_module_setting("remainsize")>get_module_setting("plantneed")){
		set_module_setting("cutdown",0);
		set_module_setting("cccount",0);
	}
	//make sure the day's growth doesn't excede the maximum for the forest
	if(get_module_setting("remainsize")>get_module_setting("fullsize")) set_module_setting("remainsize",get_module_setting("fullsize"));
	
	//reset the turns on newday-runonce if set for that
	if (get_module_setting("runonce")) {
		$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
		$res = db_query($sql);
		for ($i=0;$i<db_num_rows($res);$i++){
			$row = db_fetch_assoc($res);
			$allprefs=unserialize(get_module_pref('allprefs','lumberyard',$row['acctid']));
			$allprefs['usedlts']=0;
			$allprefs['squaresold']=0;
			set_module_pref('allprefs',serialize($allprefs),'lumberyard',$row['acctid']);
		}
	}
?>