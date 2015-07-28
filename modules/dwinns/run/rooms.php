<?
	$dwid = httpget("dwid");
	$dwname = get_module_setting("dwname","dwinns");
	page_header("Building new rooms");
	
	$sql = "SELECT rooms,stars FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	$nextstar=$row['stars']+1;
	
	$roomgold = round(get_module_setting("roomgold","dwinns") * ($nextstar * 0.5));
	$roomgems = round(get_module_setting("roomgems","dwinns") * ($nextstar * 0.5));
	
	output("`2At your %s`2, you can have up to %s guests per day, as that is the number of rooms available.",$dwname,$row['rooms']);
	output("`2To allow for more guests, you must build new rooms. This will not affect room price, though.`n`n");
	output("`2Adding a room will cost `6%s gold`2 and `4%s gems`2.`n",$roomgold,$roomgems);
	
	if($session['user']['gold']>=$roomgold && $session['user']['gems']>=$roomgems) {
		output("`2`nDo you wish to add some rooms?");
		addnav("Yes, add one room","runmodule.php?module=dwinns&op=add-room&dwid=$dwid");
		addnav(array("No, back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	}else{
		output("`2`nYou don't have enough gold or gems to add a new room to your inn.");
		addnav(array("Back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	}
?>
