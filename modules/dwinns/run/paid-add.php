<?
	$dwid = httpget("dwid");
	$dwname = get_module_setting("dwname","dwinns");
	page_header("Purchasing advertisement place");
	
	$sql = "SELECT price, stars FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	$session['user']['gold']-=$row['price'] * get_module_setting("multaddgold","dwinns");
	$session['user']['gems']-=$row['stars'] * get_module_setting("multaddgems","dwinns");
	
	output("`2You just bought advertisement space to show this %s`2 in the village.`n",$dwname);
	output("`2The advertisement contract will run off in `&30 days`2.");
	debuglog("bought village advertisement for dwelling $dwid");
	
	$sql = "UPDATE " . db_prefix("dwinns") . " SET villageadd='30' WHERE dwid='$dwid'";
	db_query($sql);
	
	addnav(array("Back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>