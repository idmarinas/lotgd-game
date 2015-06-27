<?
	$dwid = httpget("dwid");
	page_header("Purchasing a meal");
	
	$sql = "SELECT price,stars,meals FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	$pricemeal = (5 + $row['stars']) * (120 - (2 * $row['stars']));
	$meals = $row['meals'] + 1;	
	$session['user']['gold']-=$pricemeal;
	
	output("`2You send a messenger pigeon to SexyCook and just a couple of minutes later an express carriage arrives with a full meal including strawberry ice for desert. You hope nobody will notice the difference to the usual junk you cook.`n");
	output("`2`nYou now have `5%s meals`2 ready to be served.",$meals);
	debuglog("bought meal for dwelling $dwid");
	
	$sql = "UPDATE " . db_prefix("dwinns") . " SET meals='$meals', statmealsbought=statmealsbought+1 WHERE dwid='$dwid'";
	db_query($sql);
	
	if($session['user']['gold']>=$pricemeal){
		output("`2`nDo you wish to buy more meals? One meal cooked by SexyCook costs %s gold.",$pricemeal);
		addnav("Yes, buy a meal","runmodule.php?module=dwinns&op=paid-meal&dwid=$dwid");
		if($session['user']['turns']>0)
			addnav("No, cook a meal","runmodule.php?module=dwinns&op=cook-meal&dwid=$dwid"); 
	}elseif($session['user']['turns']>0){
		output("`2`nYou don't have enough gold to purchase one more meal. Do you wish to cook a meal?");
		addnav("Yes, cook a meal","runmodule.php?module=dwinns&op=cook-meal&dwid=$dwid"); 
	}else{
		output("`2`nYou neither have enough gold to purchase a meal nor enough rounds to cook one.");
	}
	addnav(array("Back to the %s",get_module_setting("dwname","dwinns")),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>
