<?
	$dwid = httpget("dwid");
	$dwname = get_module_setting("dwname","dwinns");
	page_header("Purchasing advertisement place");
	
	$sql = "SELECT price, stars, villageadd FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$sql2 = "SELECT name FROM " . db_prefix("dwellings") . " WHERE dwid='$dwid'";
	$result2 = db_query($sql2);
	$row2 = db_fetch_assoc($result2);
	
	$costaddgold = $row['price'] * get_module_setting("multaddgold","dwinns");
	$costaddgems = $row['stars'] * get_module_setting("multaddgems","dwinns");
	
	output("`2You can buy the rights to show this %s`2 in the village. These are quite costly though, and have to be renewed once in a while.`n",$dwname);	
	output("`2For this %s`2, the price to place such an add will be `6%s gold`2 and `4%s gems`2 for an `&one month`2 period.`n",$dwname,$costaddgold,$costaddgems);
	if($row2['name']=="")
		output("`n`$ You must first name your %s`$ before you can pay for an advertisement.`n",$dwname);	
	else{	
		if($row['villageadd'] > 0)
			output("`2You still have %s days`2 left on your previous advertisement contract. Signing a new one will reset the number of days to 30, not add 30 to the current amount.`n",$row['villageadd']);
		if($session['user']['gold'] >= $costaddgold && $session['user']['gems']>=$costaddgems){
			output("`2`nDo you wish to pay for this service?");
			addnav("Yes, pay for the add","runmodule.php?module=dwinns&op=paid-add&dwid=$dwid"); 
		}else{
			output("`2`nYou don't have enough gold or gems to pay for this service with you. Please come back later.");
		}
	}
	addnav(array("Back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>
