<?
	$dwid = httpget("dwid");
	$change = httpget("change");
	page_header("Changing room price");
	
	$sql = "SELECT stars, price FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$stars = $row['stars'];
	$price = $row['price'];
	
	$sql = "SELECT name FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
	$result = db_query($sql);
	$row = db_fetch_assoc($result); 
	if($row['name']==""){
		$name = translate_inline("Unnamed");
	}else 
		$name = $row['name'];
	
	$basisprice = get_module_setting("basisprice","dwinns");
	$starprice = $basisprice + ( ($basisprice / 2) * $stars); 
	$minprice = $starprice - (3 * $stars);
	$maxprice = $starprice + (3 * $stars);
	$newprice = $price + $change;
	
	output("`2You changed your room price from %s to %s.`n`n",$price,$newprice);
	
	$sql = "UPDATE " . db_prefix("dwinns") . " SET price='$newprice' WHERE dwid='$dwid'";
	db_query($sql);
	
	$price = $newprice;
	
	output("`2The current price of renting a room in %s`2 is at `6%s gold`2 per night per level. ",$name,$price);
	output("`2The minimum price for a night can be down to `6%s gold`2 or up to `6%s gold`2.`n`n",$minprice,$maxprice);
	output("`2`nDo you wish to change the price of the rent again?");
	
	if($price+25<=$maxprice)
		addnav("Yes, increase price by 25 gold","runmodule.php?module=dwinns&op=change-price&dwid=$dwid&change=25");
	if($price+5<=$maxprice)
		addnav("Yes, increase price by 5 gold","runmodule.php?module=dwinns&op=change-price&dwid=$dwid&change=5");
	if($price+1<=$maxprice)
		addnav("Yes, increase price by 1 gold","runmodule.php?module=dwinns&op=change-price&dwid=$dwid&change=1");
	
	addnav(array("Back to the %s",get_module_setting("dwname","dwinns")),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	
	if($price-1>=$minprice){
		addnav("Yes, lower price by 1 gold","runmodule.php?module=dwinns&op=change-price&dwid=$dwid&change=-1");
		if($price-5>=$minprice){
			addnav("Yes, lower price by 5 gold","runmodule.php?module=dwinns&op=change-price&dwid=$dwid&change=-5");
			if($price-25>=$minprice)
				addnav("Yes, lower price by 25 gold","runmodule.php?module=dwinns&op=change-price&dwid=$dwid&change=-25");
		}
	}
?>