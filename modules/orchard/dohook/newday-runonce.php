<?php
	//This is the Dry Rot Tree Event; reset because player can only encounter it once per system-newday
	if (get_module_setting("dryenable")==1){
		$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
		$res = db_query($sql);
		for ($i=0;$i<db_num_rows($res);$i++){
			$row = db_fetch_assoc($res);
			$allprefs=unserialize(get_module_pref('allprefs','orchard',$row['acctid']));
			$allprefs['dietreehit']=0;
			set_module_pref('allprefs',serialize($allprefs),'orchard',$row['acctid']);
		}
	}
	//This is for trees that grow only on the system newday
	if (get_module_setting("everyday")==0){
		$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
		$res = db_query($sql);
		for ($i=0;$i<db_num_rows($res);$i++){
			$row = db_fetch_assoc($res);
			$allprefs=unserialize(get_module_pref('allprefs','orchard',$row['acctid']));
			$allprefs['treegrowth']=$allprefs['treegrowth']-1;
			if ($allprefs['treegrowth']==0) $allprefs['tree']=$allprefs['tree']+1;						
			set_module_pref('allprefs',serialize($allprefs),'orchard',$row['acctid']);
		}
	}
?>