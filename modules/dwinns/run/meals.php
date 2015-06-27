<?
	$dwid = httpget("dwid");
	$dwname = get_module_setting("dwname","dwinns");
	page_header("Inside the kitchen");
	
	output("`2Inside the kitchen of your %s`2 you try to decide, if you should cook some food, or take the easy way out and just order some.",$dwname);
	output("`2You know that customers are only really satisfied with a full belly.`n`n");
	
	$sql = "SELECT stars, meals FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$pricemeal = (5 + $row['stars']) * (120 - (2 * $row['stars']));
	
	output("`2If you cook a meal, it will cost you nothing, but you'll need `&one full turn`2 to produce one meal.`n");
	output("`2On the other hand, if you order some food, it will take no time at all, but will cost you `6%s gold`2.`n",$pricemeal);
	output("`2You have `5%s meals`2 ready to be served.`n",$row['meals']);
	
	if($session['user']['gold']>=$pricemeal && $session['user']['turns']>0){
		output("`2`nDo you wish to buy or cook some meals?");
		addnav("Buy a meal","runmodule.php?module=dwinns&op=paid-meal&dwid=$dwid");
		addnav("Cook a meal","runmodule.php?module=dwinns&op=cook-meal&dwid=$dwid");
		addnav(array("No, back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	}elseif($session['user']['gold']>=$pricemeal){
			output("`2`nYou don't have enough rounds to cook a meal. Do you wish to buy a meal?");
			addnav("Yes, buy a meal","runmodule.php?module=dwinns&op=paid-meal&dwid=$dwid");
			addnav(array("No, back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	}elseif($session['user']['turns']>0){
		output("`2`nYou don't have enough gold to purchase a meal. Do you wish to cook a meal?");
		addnav("Yes, cook a meal","runmodule.php?module=dwinns&op=cook-meal&dwid=$dwid");
		addnav(array("No, back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	}else{
		output("`2`nYou neither have enough gold to purchase a meal nor enough rounds to cook one.");
		addnav(array("Back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	}
?>
