<?php
	if (get_module_setting("runonce")){
		$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
		$res = db_query($sql);
		for ($i=0;$i<db_num_rows($res);$i++){
			$row = db_fetch_assoc($res);
			$allprefs=unserialize(get_module_pref('allprefs','metalmine',$row['acctid']));
			$allprefs['usedmts']=0;
			$allprefs['drinkstoday']=0;
			$allprefs['found']=0;
			$allprefs['metalsold']=0;
			set_module_pref('allprefs',serialize($allprefs),'metalmine',$row['acctid']);
		}
	}
	increment_module_setting("dayssince",1);
?>