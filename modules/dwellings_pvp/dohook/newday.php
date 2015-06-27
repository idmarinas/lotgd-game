<?php
	$sql = "SELECT dwid,name FROM ".db_prefix("dwellings")." WHERE ownerid='{$session['user']['acctid']}'";
	$res = db_query($sql);
	$row = db_fetch_assoc($res);
	$daysleft = get_module_objpref("dwellings", $row['dwid'], "run-out", "dwellings_pvp");
	$has = get_module_objpref("dwellings", $row['dwid'], "bought", "dwellings_pvp");
	if ($has){
		if ($row['dwid'] != "" && $daysleft > 0){
			output("`n`@The Guard at your dwelling, %s`@, will expire in %s %s.`n`0",$row['name'], $daysleft, translate_inline($daysleft==1?"day":"days"));
	}else{
			output("`n`@There is no guard at your dwelling, %s`@. You may wish to renew.`n`0",$row['name']);
		}
	}
?>