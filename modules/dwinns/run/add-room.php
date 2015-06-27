<?
	$dwid = httpget("dwid");
	page_header("Adding a room");
	
	$sql = "SELECT rooms,stars FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	$roomgold = round(get_module_setting("roomgold","dwinns") * (($row['stars']+1) * 0.5));
	$roomgems = round(get_module_setting("roomgems","dwinns") * (($row['stars']+1) * 0.5));
	
	$session['user']['gold']-=$roomgold;
	$session['user']['gems']-=$roomgems;
	output("`2You add a new room to your establishment.`n");
	$sql2 = "UPDATE " . db_prefix("dwinns") . " SET rooms=rooms+1 WHERE dwid='$dwid'";
	db_query($sql2);
	$sql2 = "UPDATE " . db_prefix("dwellings") . " SET goldvalue=goldvalue+round($roomgold*0.3),gemvalue=gemvalue+round($roomgems*0.3) WHERE dwid='$dwid'";
	db_query($sql2);
	
	output("`2`nYou now have `5%s rooms`2 in your establishment.",$row['rooms']+1);
	debuglog("built room for dwelling $dwid");
	if($session['user']['gold']>=$roomgold && $session['user']['gems']>=$roomgems) {
		output("`2`nDo you wish to add more rooms?");
		addnav("Yes, add one room","runmodule.php?module=dwinns&op=add-room&dwid=$dwid");
		addnav(array("No, back to the %s",get_module_setting("dwname","dwinns")),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	}else{
		output("`2`nYou don't have enough gold or gems to add a new room to your inn.");
		addnav(array("Back to the %s",get_module_setting("dwname","dwinns")),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	}
?>