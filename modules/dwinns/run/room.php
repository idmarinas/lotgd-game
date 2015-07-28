<?
	$dwid = httpget("dwid");
	$dwname = get_module_setting("dwname","dwinns");
	page_header("Getting a room");
	
	$sdwid = get_module_pref("sleepingindwinn");
	if($sdwid!=0 && $sdwid!=$dwid){
		output("You already paid for a room for today. Use that one and let others sleep here, will ya?");
	}else{
		$sql = "SELECT price, stars, rooms, guests FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		
		$price = $row['price']* $session['user']['level'];
		
		output("`2There are %s rooms available in this establishment, %s guests are already staying for the night.`n`n",$row['rooms'],$row['guests']);
		
		
		if(get_module_pref("sleepingindwinn")!=$dwid){
			output("Renting a room costs `6%s gold`2. Once you've rented the room, it stays rented until the day is over.`n",$price);
			output("Sleeping in %s`2 with bad ratings (a low amount of stars) doesn't leave you refreshed. Most times you'll wake up with pains in your back or headaches.",get_module_setting("dwnameplural","dwinns"));
			output(" Yet a night in an %s`2 with good ratings leaves you refreshed and you wake up in a good mood and more than ready to kill everthing and everyone on sight.",get_module_setting("dwnameplural","dwinns"));
			output("`n`nThis %s`2 has a star rating of %s. Do you wish to rent a room here?`n`n",$dwname,$row['stars']);
			if($row['rooms'] > $row['guests'])
				if($session['user']['gold'] >= $price)
					addnav("Rent a room","runmodule.php?module=dwinns&op=room-rent&dwid=$dwid");
				else
					output("`n`nYou don't have enough gold to rent a room here.");
			else
				output("This establishment is full. Try another one tonight.`n");
		}else{
			output("`n`nYou have already rented a room in this establishment.`nDo you want to go to sleep?");
			addnav("Go to sleep (Logout)","runmodule.php?module=dwellings&op=logout&dwid=$dwid&type=dwinns&rent=true");
		}
	}
	addnav(array("Back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>
