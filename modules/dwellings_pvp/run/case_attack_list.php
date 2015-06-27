<?php
	page_header("PvP Roster");
	output("`3Before you stands a large roster of who is sleeping in the current house you are looking at.");
	output("Pick out your target and hunt them down!`n`n");
	$days = getsetting("pvpimmunity",5);
	$exp = getsetting("pvpminexp",1000);
	$id = $session['user']['acctid'];
	$loc = $session['user']['location'];
	$typeid = httpget('typeid');
	$top = $session['user']['level']+get_module_objpref("dwellingtypes",$typeid,"top-band","dwellings_pvp");
	$bottom = $session['user']['level']-get_module_objpref("dwellingtypes",$typeid,"bottom-band","dwellings_pvp");
	$extra = "";
	$join = "";
	if (is_module_active("pvpimmunity")){
		$extra = "AND (setting = 'check_willing' AND modulename='pvpimmunity' AND value = 1)";
		$join = "INNER JOIN ".db_prefix("module_userprefs")." ON acctid=userid";
	}
	if (!get_module_setting("altlist")){
		$sql = "SELECT acctid, dragonkills, name, alive, a.value AS location, sex, level, laston, loggedin, login, pvpflag, clanshort, clanrank, lastip, uniqueid
		FROM $ac
		LEFT JOIN $cl ON $cl.clanid=$ac.clanid
		INNER JOIN $mu AS a ON $ac.acctid=a.userid
		INNER JOIN $mu AS b ON $ac.acctid=b.userid
		$join
		WHERE (locked=0)
		AND (a.setting = 'location_saver' AND a.modulename = 'dwellings')
		AND (b.setting = 'dwelling_saver' AND b.modulename='dwellings' AND b.value = '$dwid')
		$extra
		AND (slaydragon=0) AND
		(age>$days OR dragonkills>0 OR pk>0 OR experience>$exp)
		AND (level>=$bottom AND level<=$top) AND (alive=1)
		AND (laston<'$last' OR loggedin=0) AND (acctid<>$id)
		AND (lastip != '{$session['user']['lastip']}')
		AND (uniqueid != '{$session['user']['uniqueid']}')
		ORDER BY location='$loc' DESC, location, level DESC,
		experience DESC, dragonkills DESC";
		output_notl("`c");
		$link = "runmodule.php?module=dwellings_pvp";
		$extra = "&op=fight1";
		require_once("lib/pvplist.php");
		pvplist($loc, $link, $extra, $sql);
		output_notl("`c`0");
	}else{
		$sql = "SELECT acctid, dragonkills, name, title, alive, a.value AS location, sex, level, laston, loggedin, pvpflag, lastip, uniqueid
		FROM $ac
		INNER JOIN $mu AS a ON $ac.acctid=a.userid
		INNER JOIN $mu AS b ON $ac.acctid=b.userid
		WHERE (locked=0)
		AND (a.setting = 'location_saver' AND a.modulename = 'dwellings')
		AND (b.setting = 'dwelling_saver' AND b.modulename='dwellings' AND b.value = '$dwid')
		AND (slaydragon=0) AND
		(age>$days OR dragonkills>0 OR pk>0 OR experience>$exp)
		AND (level>=$bottom AND level<=$top) AND (alive=1)
		AND (laston<'$last' OR loggedin=0) AND (acctid<>$id)
		AND (lastip != '{$session['user']['lastip']}')
		AND (uniqueid != '{$session['user']['uniqueid']}')
		ORDER BY location='$loc' DESC, location, level DESC,
		experience DESC, dragonkills DESC";
		// Following code is liberated from lib/pvplist.php
		$res = db_query($sql);
		$num = db_num_rows($res);
		
		$pvp = array();
		for ($i = 0; $i < $num; $i++){
			$row = db_fetch_assoc($res);
			$pvp[] = $row;
		}			
		$pvp = modulehook("pvpmodifytargets", $pvp);
		
		tlschema("pvp");
		$n = translate_inline("Title");
		$l = translate_inline("Level");
		$loca = translate_inline("Location");
		$ops = translate_inline("Ops");
		$att = translate_inline("Attack");
		$link = "runmodule.php?module=dwellings_pvp";
		$extra = "&op=fight1";
		rawoutput("<table align='center' border='0' cellpadding='3' cellspacing='0'>");
		rawoutput("<tr class='trhead'><td>$n</td><td>$l</td><td>$loca</td><td>$ops</td></tr>");
		$loc_counts = array();
		$num = count($pvp);
		$j = 0;
		for ($i = 0; $i < $num; $i++){
			$row = $pvp[$i];
			if (isset($row['invalid']) && $row['invalid']) continue;
			if (!isset($loc_counts[$row['location']]))
				$loc_counts[$row['location']] = 0;
			$loc_counts[$row['location']]++;
			if ($row['location'] != $loc) continue;
			$j++;
			rawoutput("<tr class='".($j%2?"trlight":"trdark")."'>");
			rawoutput("<td>");
			output_notl("`@%s`0", $row['title']);
			rawoutput("</td>");
			rawoutput("<td>");
			output_notl("%s", $row['level']);
			rawoutput("</td>");
			rawoutput("<td>");
			output_notl("%s", $row['location']);
			rawoutput("</td>");
			rawoutput("<td>[ ");
			if($row['pvpflag']>$pvptimeout){
				output("`i(Attacked too recently)`i");
			}elseif ($loc!=$row['location']){
				output("`i(Can't reach them from here)`i");
			}else{
				rawoutput("<a href='$link$extra&name=".rawurlencode($row['acctid'])."'>$att</a>");
				addnav("","$link$extra&name=".rawurlencode($row['acctid']));
			}
			rawoutput(" ]</td>");
			rawoutput("</tr>");
		}
	
		if (!isset($loc_counts[$loc]) || $loc_counts[$loc]==0){
			$noone = translate_inline("`iThere are no available targets.`i");
			output_notl("<tr><td align='center' colspan='4'>$noone</td></tr>", true);
		}
		rawoutput("</table>");
		tlschema();
	}
	$p = httpget('p');
	$s = httpget('s');
	$sh = httpget('sh');
	$o = httpget('o');
	addnav("Actions");
	addnav("Refresh List","runmodule.php?module=dwellings_pvp&op=attack_list&dwid=$dwid&p=$p&s=$s&sh=$sh&o=$o");
	addnav("Leave");
	addnav("Hamlet Registry","runmodule.php?module=dwellings&op=list&ref=hamlet&sortby=$s&showonly=$sh&order=$o&page=$p");
?>