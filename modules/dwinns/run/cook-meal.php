<?
	$dwname = get_module_setting("dwname","dwinns");
	$dwid = httpget("dwid");
	page_header("Cooking a meal");
	
	$sql = "SELECT meals, price, stars FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	$meals = $row['meals'] + 1;
	$session['user']['turns']-=1;
	$pricemeal = (5 + $row['stars']) * (120 - (2 * $row['stars']));
	
	output("`2You fearfully open the cabinets, where spiders and roaches flee the unexpected light. You scrape off some green mold, wash away what seems to have dust, or insect eggs, cook it for a couple of minutes and put the strange meal on to some not too clean plates. With any luck this won't kill any customers, at least not while they still sit at your %s.`n",$dwname);
	output("`2`nYou now have `5%s meals`2 ready to be served.",$meals);
	debuglog("cooked meal for dwelling $dwid");
	
	$sql = "UPDATE " . db_prefix("dwinns") . " SET meals='$meals' WHERE dwid='$dwid'";
	db_query($sql);
	
	if($session['user']['turns']>0){
		output("`2`nDo you wish to cook more meals?");
		addnav("Yes, cook a meal","runmodule.php?module=dwinns&op=cook-meal&dwid=$dwid");
		if($session['user']['gold']>=$pricemeal)
			addnav("No, buy a meal","runmodule.php?module=dwinns&op=paid-meal&dwid=$dwid");
	}elseif($session['user']['gold']>=$pricemeal){
		output("`2`nYou don't have enough rounds to cook more meals. Do you wish to buy a meal?");
		addnav("Yes, buy a meal","runmodule.php?module=dwinns&op=paid-meal&dwid=$dwid");
	}else{
		output("`2`nYou neither have enough gold to purchase a meal nor enough rounds to cook one.");
	}
	addnav(array("Back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>
