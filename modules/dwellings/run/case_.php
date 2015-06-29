<?php
	$enter = translate_inline("Enter");
	page_header("Dwellings");
	if(get_module_pref("dwelling_saver")>0){
		$session['user']['location'] = get_module_pref("location_saver");
		set_module_pref("dwelling_saver",0);
	}else{
	  set_module_pref("location_saver",$session['user']['location'],"dwellings");
	}
	if($cityid == ""){
		require_once("modules/cityprefs/lib.php");
		$cityid = get_cityprefs_cityid("location",$session['user']['location']);
	}
	output("You leave the village in search of a place to dwell.");
	addnav("The Hamlet Registry","runmodule.php?module=dwellings&op=list&ref=hamlet");
	addnav("Dwellings for Sale","runmodule.php?module=dwellings&op=forsale");
	// Let SQL do, what SQL can do... No need to select *everything* here... just count the rows...
	$sql = "SELECT COUNT(dwid) AS count FROM ".db_prefix("dwellings")." WHERE location='".$session['user']['location']."'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$allsumloc = $row['count'];
// We don't need this anymore, so we will free up the consumed memory.
	db_free_result($result);
	$sql = "SELECT * FROM ".db_prefix("dwellings")." WHERE location='".$session['user']['location']."' AND ownerid=".$session['user']['acctid']." ORDER BY type DESC";
	$result = db_query($sql);
	$sumownedloc=db_num_rows($result);
	$allowbuy=1;
	if(get_module_objpref("city",$cityid,"allcitylimit")!=0 
		&& ($allsumloc >= get_module_objpref("city",$cityid,"allcitylimit"))){
		$allowbuy = 0;
		output("`n`n`^If you're thinking about setting up a dwelling here, good luck finding available property.`0`n`n");
	}elseif(get_module_objpref("city",$cityid,"ownercitylimit")!=0 
		&& ($sumownedloc >= get_module_objpref("city",$cityid,"ownercitylimit"))){
		$allowbuy = 0;
		output("`n`n`^Due to city restrictions on the number of dwellings you already own here, you cannot build another dwelling here.`0`n`n");
	}
	modulehook("dwellings",array("allowbuy"=>$allowbuy,"cityid"=>$cityid));
	output("`n`n`cYou own the following completed dwellings here:`c`n");
	$loc = $session['user']['location'];
	$tname = translate_inline("Name");
	$towner = translate_inline("Owner");
	$ttype = translate_inline("Type");		
	$tdesc = translate_inline("Description");
	rawoutput("<table border='0' cellpadding='3' cellspacing='0' align='center'>");
	rawoutput("<tr class='trhead'><td style='width:180px'>$tname</td><td style='width:300px'>$tdesc</td><td>$ttype</td><td>&nbsp;</td></tr>"); 
	if(!db_num_rows($result)){
		$none = translate_inline("None");
		rawoutput("<tr class='trdark'><td align=center colspan=4><i>$none</i></td></tr>");
	}else{
		$outed = 0;
		$i = 0;
		while($row = db_fetch_assoc($result)){
			$rtype = $row['type'];
			$rdwid = $row['dwid'];
			if($row['name']==""){
				$name = translate_inline("Unnamed");
			}else{
				$name = $row['name'];
			}
			$cname = translate_inline(get_module_setting("dwname",$rtype));
			if ($row['status']==2){
				addnav("Make a Payment...");
				addnav(array("On your %s",$cname),
					"runmodule.php?module=dwellings&op=buy&type=$rtype&subop=payment&dwid=$rdwid");
			}elseif($row['status']==3 /*&& 
				(get_module_objpref("dwellings",$rdwid,"buildturns")<get_module_setting("turncost",$rtype))*/){
				addnav("Construction on...");
				addnav(array("Your %s",$cname),
					"runmodule.php?module=dwellings&op=build&type=$rtype&dwid=$rdwid");					
			}elseif($row['status']==1){
				$outed++;
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
				// Changed this, so coloured names will display correctly
				$name = appoencode($name);
				$cname = appoencode($cname);
				rawoutput($name);
				rawoutput("</td><td>");
				rawoutput(appoencode($row['windowpeer']));
				rawoutput("</td><td>");
				rawoutput($cname);
				rawoutput("</td>");
				rawoutput("<td nowrap>[<a href='runmodule.php?module=dwellings&op=enter&dwid=$rdwid'>");
				output_notl($enter);
				rawoutput("</a>]</td></tr>");
				addnav("","runmodule.php?module=dwellings&op=enter&dwid=$rdwid");
			}
			if($row['status'] != 1 && $session['user']['level'] >= get_module_setting("levelsell")){
				addnav("Demolish...");
				addnav(array("Your %s",$cname),
					"runmodule.php?module=dwellings&op=demo&dwid=$rdwid");
			}
			// Hmmm... this hook would have only triggered dwellings with the status 4 or 5 (which we won't be the owner of anymore...)
			// So I moved this here...
			modulehook("dwellings-owned",array("type"=>$rtype,"dwid"=>$rdwid,"status"=>$row['status']));
			$i++;
		}
		if($outed == 0){ 
			$none = translate_inline("None"); 
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td align=center colspan=3><i>$none</i></td></tr>"); 
		}
	}
	rawoutput("</table>");
	output("`n`n`cYou have keys to the following dwellings here:`c`n");
	$dwellings = db_prefix("dwellings");
	$dwellingkeys = db_prefix("dwellingkeys");
	$sql = "SELECT $dwellings.name AS name,
			$dwellings.dwid AS dwid,
			$dwellings.ownerid AS ownerid,
			$dwellings.type AS type,
			$dwellings.status AS status,
			$dwellings.windowpeer AS windowpeer, 
			$dwellingkeys.keyowner FROM $dwellingkeys 
			INNER JOIN $dwellings ON $dwellings.dwid = $dwellingkeys.dwid 
			WHERE $dwellingkeys.dwidowner != ".$session['user']['acctid']." 
			AND $dwellingkeys.keyowner = ".$session['user']['acctid']." 
			AND $dwellings.status = 1
			AND $dwellings.location = '".$session['user']['location']."'	
			ORDER BY $dwellingkeys.keyid DESC";
	$result = db_query($sql);
	rawoutput("<table border='0' cellpadding='3' cellspacing='0' align=center>");
	rawoutput("<tr class='trhead'><td style='width:100px;' nowrap>$tname</td><td style='width:100px;' nowrap>$towner</td><td style='width:300px;'>$tdesc</td><td>$ttype</td><td>&nbsp;</td></tr>"); 
	$i = 0;
	if(!db_num_rows($result)){
		$none = translate_inline("None");
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td align=center  colspan=4><i>$none</i></td></tr>");
	}else{
		while ($row = db_fetch_assoc($result)){
			$type = $row['type'];
			$sql2 = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=".$row['ownerid']."";
			$result2 = db_query($sql2);
			$row2 = db_fetch_assoc($result2);
			$owner = $row2['name'];
			$dwid = $row['dwid'];
			$cname = translate_inline(get_module_setting("dwname",$row['type']));
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
			if($row['name'] == ""){
				$name = translate_inline("Unnamed");
			}else{
				$name = $row['name'];
			}
			output_notl($name);
			rawoutput("</td><td>");
			output_notl($owner);
			rawoutput("</td><td>");
			rawoutput(appoencode($row['windowpeer']));
			rawoutput("</td><td>");
			output_notl($cname);
			rawoutput("</td>");
			rawoutput("<td nowrap>[<a href='runmodule.php?module=dwellings&op=enter&dwid=$dwid'>");
			output_notl($enter);
			rawoutput("</a>]</td></tr>");
			addnav("","runmodule.php?module=dwellings&op=enter&dwid=$dwid");
			$i++;
		}
	}	
	rawoutput("</table>");
?>