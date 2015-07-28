<?
	$dwname = get_module_setting("dwname","dwinns");
	if(get_module_objpref("city",$args['cityid'],"showdwinns")){
		output("  `2The newest addition to the dwellings area seems to be player owned %s`2, which seem to be under some remodeling still.`0",translate_inline(get_module_setting("dwnameplural")));
		if($args['allowbuy']==1 && $session['user']['dragonkills']>=get_module_setting("dkreq")){
			$cityid=$args['cityid'];
			addnav("Options");
			addnav(array("Establish a %s",translate_inline(ucfirst($dwname))),"runmodule.php?module=dwellings&op=buy&type=dwinns&subop=presetup&cityid=$cityid");
		}
	}
	$tname = translate_inline("Name");
	$tstars = translate_inline("Stars");
	$tdesc = translate_inline("Description");
	$enter = translate_inline("Enter");
	$location = $session['user']['location'];
	$sql = "SELECT name, dwid, windowpeer FROM " . db_prefix("dwellings") . " WHERE type='dwinns' AND status=1 AND location='$location'";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
	 output("`n`n`n`n`cYou can find music, screaming and ale flowing out of the following establishments:`c`n");
	 rawoutput("<table border='0' cellpadding='3' cellspacing='0' align='center'><tr class='trhead'><td style=\"width:150px\">$tname</td><td style='width:350px' align=center>$tdesc</td><td align=center>$tstars</td><td align=center style='width:75px'>&nbsp;</td></tr>"); 
	
	 for($i = 0; $i < db_num_rows($result); $i++){
	 	$row = db_fetch_assoc($result);
	 	if($row['name']==""){
	 		$name = translate_inline("Unnamed");
	 	}else{
	 		$name = $row['name'];
	 	}
	 	$rdwid = $row['dwid'];
	 	$name = appoencode($name);
		$sql2 = "SELECT stars, closed FROM " . db_prefix("dwinns") . " WHERE dwid=$rdwid";
		$result2 = db_query($sql2);
		$row2 = db_fetch_assoc($result2);
		$stars=$row2['stars'];
		$closed=$row2['closed'];
		switch($stars){
			case 0: $stars = appoencode("`)-"); break;
			case 1: $stars = appoencode("`)*"); break;
			case 2: $stars = appoencode("`)**"); break;
			case 3: $stars = appoencode("`)***"); break;
			case 4: $stars = appoencode("`Q****"); break;
			case 5: $stars = appoencode("`Q*****"); break;
			case 6:	$stars = appoencode("`Q******"); break;
			case 7: $stars = appoencode("`7*******"); break;
			case 8: $stars = appoencode("`7********"); break;
			case 9: $stars = appoencode("`^*********"); break;
			case 10: $stars = appoencode("`^**********"); break;
		}
	 	rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
	 	rawoutput("$name</td><td>".appoencode($row['windowpeer'])."</td><td>$stars</td>");
		 if($closed==0)
		 	rawoutput("<td>[ <a href=runmodule.php?module=dwellings&op=enter&dwid=$rdwid>$enter</a> ]</td></tr>");
		 else{
		 	$tclosed = translate_inline("Closed");
		 	rawoutput("<td align=center> - $tclosed - </td></tr>");
		 }
	 	addnav("","runmodule.php?module=dwellings&op=enter&dwid=$rdwid");
	 }
  	 rawoutput("</table>");
	}
?>
