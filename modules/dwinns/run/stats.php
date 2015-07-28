<?
	$dwid = httpget("dwid");
	$reset = httpget("reset");
	$sql2 = "SELECT name FROM " . db_prefix("dwellings") . " WHERE dwid='$dwid'";
	$result2 = db_query($sql2);
	$row2 = db_fetch_assoc($result2);

	$dwname = get_module_setting("dwname","dwinns");
	page_header("%s's Statistics",sanitize($row2['name']));
	
	if($reset==1){
		output("`4You throw every scrap of paper that you can find into the open fire. You should remember what profit was made today, but to be sure you start a new page in your register and only add information starting now.`n`n`n");
		
		$sql = "UPDATE " . db_prefix(dwinns) . " SET statroomsprofit=0, statmealsprofit=0, statdrinksprofit=0, statrooms=0, statdrinks=0, statmeals=0, statmealsbought=0, statticks=0	WHERE dwid='$dwid'";
	 	db_query($sql);
				
	}

	$sql = "SELECT * FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	$guards = get_module_objpref("dwellings", $dwid, "bought", "dwellings_pvp");
	if($guards==0) $guard=translate_inline("No");
	else $guard=translate_inline("Yes, for %s more days",get_module_objpref("dwellings", $dwid, "run-out", "dwellings_pvp"));
	
	output("`2From bills and receipts on little scraps of paper (and oddly enough, a sock) from dozens of clerks and maids you try to figure out exactly if you're actually making money out of this place.`n`n");
	output("`2The following statistics are only accurate up to the time you last burned your bills and receipts.`n`n");
	output("`2%s `2name: `&%s`n",$dwname,$row2['name']);	
	
	rawoutput("<br><hr><br>");
	
	output("`QNumber of rooms: `&%s`n",$row['rooms']);	
	output("`QNumber of stars: `&%s`n",$row['stars']);	
	output("`QNumber of guests during the last %s days: `&%s`n",$row['statticks'],$row['statrooms']);	
	output("`QProfit made from rooms during the last %s days: `&%s gold`n",$row['statticks'],$row['statroomsprofit']*0.9);	
	output("`QProfit made from rooms today: `&%s gold`n",$row['logrooms']*0.9);	
	if($row['statroomsprofit']+$row['statmealsprofit']+$row['statdrinksprofit']>0){
		$total=($row['statroomsprofit']+$row['statmealsprofit']+$row['statdrinksprofit']);
		$percentrooms = round($row['statroomsprofit']/$total*100);
		$percentmeals = round($row['statmealsprofit']/$total*100);
		$percentdrinks = round($row['statdrinksprofit']/$total*100);
	}	
	else{
		$percentrooms = 0;
		$percentmeals = 0;
		$percentdrinks = 0;
	}
	output("`QPercentage of the overall profit from room rent during the last %s days: `&%s%%`n",$row['statticks'],$percentrooms);	

	rawoutput("<br><hr width=75% align=center><br>");

	output("`3Name of the ale of the house: `&%s`n",$row['brewname']);	
	output("`3Liters of ale stored: `&%s`n",$row['drinks']);	
	output("`3Liters of ale brewed until now: `&%s`n",$row['brewexp']);
	switch($row['drinkqual']){
		case 0: $drinkqual=translate_inline("None"); break;	
		case 1: $drinkqual=translate_inline("Low"); break; 
		case 2: $drinkqual=translate_inline("Normal"); break;
		case 3: $drinkqual=translate_inline("High"); break;
	}	
	output("`3License to brew ale of quality: `&%s`n",$drinkqual);	
	output("`3The license to brew ale runs out in `&%s`3 days`n",$row['brewdays']);	
	output("`3Number of ales sold during the last %s days: `&%s`n",$row['statticks'],$row['statdrinks']);	
	output("`3Profit made from selling ales during the last %s days: `&%s gold`n",$row['statticks'],$row['statdrinksprofit']*0.9);	
	output("`3Profit made from selling ales today: `&%s gold`n",$row['logdrinks']*0.9);	
	output("`3Percentage of the overall profit from selling ales during the last %s days: `&%s%%`n",$row['statticks'],$percentdrinks);	

	rawoutput("<br><hr width=75% align=center><br>");

	output("`GNumber of meals stored (cooked and/or ordered): `&%s`n",$row['meals']);	
	output("`GNumber of meals ordered from SexyCook during the last %s days: `&%s`n",$row['statticks'],$row['statmealsbought']);	
	output("`GTotal cost of meals ordered from SexyCook during the last %s days: `&%s gold`n",$row['statticks'],(5 + $row['stars']) * (120 - (2 * $row['stars']))*$row['statmealsbought']);
	output("`GNumber of meals sold during the last %s days: `&%s`n",$row['statticks'],$row['statmeals']);	
	output("`GProfit made from selling meals during the last %s days (includes the cost of ordered meals): `&%s gold`n",$row['statticks'],($row['statmealsprofit']*0.9)-(5 + $row['stars']) * (120 - (2 * $row['stars']))*$row['statmealsbought']);	
	output("`GProfit made from selling meals today: `&%s gold`n",$row['logmeals']*0.9);	
	output("`GPercentage of the overall profit from selling meals during the last %s days: `&%s%%`n",$row['statticks'],$percentmeals);	

	addnav("Burn the older receipts","runmodule.php?module=dwinns&op=stats&reset=1&dwid=$dwid");
	addnav(array("Back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>
