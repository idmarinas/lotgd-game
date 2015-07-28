<?
	$dwid = httpget("dwid");
	$drinkqual = httpget("drinkqual");
	page_header("Purchasing a brewing license");
	
	switch($drinkqual){
		case 1: $session['user']['gold']-=1000; break;
		case 2: $session['user']['gold']-=10000; break;
		case 3: $session['user']['gold']-=100000; break;
	}
	
	$sql = "UPDATE " . db_prefix("dwinns") . " SET drinkqual='$drinkqual', brewdays='30' WHERE dwid='$dwid'";
	db_query($sql);
	$sql = "SELECT brewname FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	output("`2You purchased a license to brew %s.`n",$row['brewname']);
	output("`2This license will expire in 30 days.`n");
	debuglog("bought license $drinkqual for dwelling $dwid");
	
	addnav(array("Back to the %s",get_module_setting("dwname","dwinns")),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	addnav("Back to the Brewery","runmodule.php?module=dwinns&op=drinks&dwid=$dwid");
?>