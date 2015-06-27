<?php
	$args['handled'] = 1;
	$location = get_module_setting("logoutlocation","dwellings");
	$last = date("Y-m-d H:i:s", strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
	if ($args['loc'] == $location){
		$extra = "";
		$join = "";
		if (is_module_active("pvpimmunity")){
			$extra = "AND (setting = 'check_willing' AND modulename='pvpimmunity' AND value = 1)";
			$join = "INNER JOIN ".db_prefix("module_userprefs")." ON acctid=userid";
		}
		$days = getsetting("pvpimmunity",5);
		$exp = getsetting("pvpminexp",1000);
		$id = $session['user']['acctid'];
		$sql = "SELECT acctid,level FROM ".db_prefix("accounts")." 
				$join
				WHERE location='$location'
				AND (pvpflag < '$pvptimeout')
				AND (alive=1) AND (locked=0) AND (slaydragon=0) 
				AND age>$days OR dragonkills>0 OR pk>0 OR experience>$exp)
				AND (alive=1)
				AND (laston<'$last' OR loggedin=0) AND (acctid<>$id)
				AND (lastip != '{$session['user']['lastip']}')
				AND (uniqueid != '{$session['user']['uniqueid']}')
				$extra";
		$res = db_query($sql);
		debug($sql);
		$count = 0;
		require_once("modules/dwellings/func.php");
		for($i = 0; $i < db_num_rows($res); $i++){
			$row = db_fetch_assoc($res);
			$dwid = get_module_pref("dwelling_saver","dwellings",$row['acctid']);
			$sql2 = "SELECT type FROM ".db_prefix("dwellings")." WHERE dwid=$dwid AND status=1";
			$res2 = db_query($sql2);
			$row2 = db_fetch_assoc($res2);
			$typeid = get_typeid($row['type']);
			$has_guard = get_module_objpref("dwellings", $dwid, "bought", "dwellings_pvp");
			$top_band = get_module_objpref("dwellingtypes",$typeid,"top-band","dwellings_pvp");
			$bottom_band = get_module_objpref("dwellingtypes",$typeid,"bottom-band","dwellings_pvp");
			if (!$has_guard
				&& $row['level'] <= $session['user']['level']+$top_band
				&& $row['level'] >= $session['user']['level']-$bottom_band) $count++;
			debug($count);
		}
		if ($count != 0){
			if ($count == 1) {
				output("`&There is `^1`& person sleeping in the %s.`0`n",$location);
			}else{
			    output("`&There are `^%s`& people sleeping in the %s.`0`n", $count,$location);
			}
		}
	}
?>