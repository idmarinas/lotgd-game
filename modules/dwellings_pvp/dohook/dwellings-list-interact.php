<?php
	$fail = 0;
	$last = date("Y-m-d H:i:s", strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
	if (is_module_active("pvpimmunity") && !get_module_pref("check_willing","pvpimmunity")) $fail = 1;
	if (!$fail){
		$typeid = get_module_setting("typeid",$args['type']);
		if ($session['user']['location'] == $args['location'] 
			&& get_module_objpref("dwellingtypes", $typeid, "pvp", "dwellings_pvp")
			&& !get_module_objpref("dwellings", $args['dwid'], "bought", "dwellings_pvp")
			&& $session['user']['playerfights'] > 0
			&& $args['status'] == 1){
			$top = $session['user']['level']+get_module_objpref("dwellingtypes",$typeid,"top-band","dwellings_pvp");
			$bottom = $session['user']['level']-get_module_objpref("dwellingtypes",$typeid,"bottom-band","dwellings_pvp");
			$sql = "SELECT count(acctid) AS count FROM ".db_prefix("accounts")." 
					INNER JOIN ".db_prefix("module_userprefs")." 
					ON acctid=userid 
					WHERE (level>=$bottom && level<=$top)
					AND (laston < '$last' OR loggedin=0)
					AND (pvpflag < '$pvptimeout')
					AND (alive=1) AND (locked=0) AND (slaydragon=0)
					AND modulename='dwellings'
					AND setting='dwelling_saver' 
					AND value='{$args['dwid']}'";
			$res = db_query($sql);
			$row = db_fetch_assoc($res);
			$dwid = $args['dwid'];
			if ($row['count'] > 0){
				$p = httpget('page');
				$s = httpget('sortby');
				$sh = httpget('showonly');
				$o = httpget('order');
				$temp = sprintf_translate("Slay (%s)",$row['count']);  
				rawoutput("<a href='runmodule.php?module=dwellings_pvp&op=attack_list&dwid=$dwid&typeid=$typeid&p=$p&s=$s&sh=$sh&o=$o'>$temp</a><br>");  
				addnav("","runmodule.php?module=dwellings_pvp&op=attack_list&dwid=$dwid&typeid=$typeid&p=$p&s=$s&sh=$sh&o=$o");
			}else{
				// output_notl("`c`i");
				// output("None");
				// output_notl("`i`c`0");
			}
		}
	}
?>