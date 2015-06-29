<?php
	//set the stone price for the day
	$blockpay=(e_rand(get_module_setting("blockpaymin"),get_module_setting("blockpaymax")));
	set_module_setting("blockpay",$blockpay);

	//if not using lost ruins, the stone giants attack by counters.
	if ((is_module_active('lostruins') && get_module_setting("usequarry")==1) || is_module_active("lostruins")==0) {
		if (e_rand(1,100)<=get_module_setting("sgpercent") && get_module_setting("underatk")==0) increment_module_setting("sgcount",1);
		if (get_module_setting("sgcount")>=get_module_setting("giantatk")) {
			set_module_setting("underatk",1);
			set_module_setting("giantleft",get_module_setting("numbgiant"));
			set_module_setting("sgcount",0);
			$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
			$res = db_query($sql);
			//this resets the announcements so that players are told when the attacks start/end in the newday screen
			for ($i=0;$i<db_num_rows($res);$i++){
				$row = db_fetch_assoc($res);
				$allprefs=unserialize(get_module_pref('allprefs','quarry',$row['acctid']));
				$allprefs['sgattack']=0;
				$allprefs['sgend']=0;
				set_module_pref('allprefs',serialize($allprefs),'quarry',$row['acctid']);
			}
		}
	}
	//reset the turns on newday-runonce if set for that
	if (get_module_setting("runonce")){
		$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
		$res = db_query($sql);
		for ($i=0;$i<db_num_rows($res);$i++){
			$row = db_fetch_assoc($res);
			$allprefs=unserialize(get_module_pref('allprefs','quarry',$row['acctid']));
			$allprefs['usedqts']=0;
			$allprefs['sgfought']=0;
			$allprefs['insured']=0;
			$allprefs['stonesold']=0;
			set_module_pref('allprefs',serialize($allprefs),'quarry',$row['acctid']);
		}
	}
	//reset the offer to join masons society
	if (is_module_active("masons")){
		$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
		$res = db_query($sql);
		for ($i=0;$i<db_num_rows($res);$i++){
			$row = db_fetch_assoc($res);
			$allprefs=unserialize(get_module_pref('allprefs','masons',$row['acctid']));
			$allprefs['offermember']=0;
			set_module_pref('allprefs',serialize($allprefs),'masons',$row['acctid']);
		}
	}
?>