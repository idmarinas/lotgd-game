<?
	$dwid = httpget("dwid");
	page_header("Renting a room");
	
	$sql = "SELECT price FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	$price = $row['price']* $session['user']['level'];
	$session['user']['gold']-=$price;
	
	set_module_pref("sleepingindwinn", $dwid);
	$sql = "UPDATE " . db_prefix("dwinns") . " SET logrooms=logrooms+$price, guests=guests+1, statroomsprofit=statroomsprofit+$price, statrooms=statrooms+1 WHERE dwid='$dwid'";
	db_query($sql);
	
	output("`2You rent a room.`n`n");
	
	addnav("Go to sleep (Logout)","runmodule.php?module=dwellings&op=logout&dwid=$dwid&type=dwinns");
	addnav(array("Back to the %s",get_module_setting("dwname","dwinns")),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>
