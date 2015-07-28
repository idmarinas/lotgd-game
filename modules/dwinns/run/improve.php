<?
	$dwid = httpget("dwid");
	$dwname = get_module_setting("dwname","dwinns");
	page_header("Improving the place");
	
	$sql = "SELECT stars,closed FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$nextstar = $row['stars']+1;

	output("`2At %s`2, the more improvements you add to the place, the more welcome your guests will feel. This will add to the price of the rooms, but don't worry, they'll feel good giving you more money, as they can sleep and eat better.`n`n",$dwname,$rooms);
	output("`2By purchasing these improvements, some guys with clipboards will come around and check the place. You'll probably get a new star, but caution, forgetting to maintain the place will take the star (and the improvement) away (not implemented yet).`n`n");
	
	if($nextstar < 11){
		$costimpgold = get_module_setting("multimpgold","dwinns") * $nextstar;
		$costimpgems = get_module_setting("multimpgems","dwinns") * $nextstar;
		$starwood = 0;
		$starstone = 0;
		$wood = 0;
		$stone = 0;
		if (is_module_active('lumberyard'))
			$starwood = $nextstar;
		if (is_module_active('quarry'))
			$starstone = $nextstar;
		output("`2Purchasing improvement nr. %s will cost you `6%s gold`2 and `4%s gems`2.`n",$nextstar,$costimpgold,$costimpgems);
		if($starwood>0 || $starstone>0){
			output("`2Additionally, this improvement will cost you `Q%s wood`2 and `7%s stone`2 for a large number of small little things (e.g. a house for the cat, renovations in the kitchen, a pair of new beds, etc).`n",$starwood,$starstone);
			$allprefs=unserialize(get_module_pref('allprefs','lumberyard'));
			$wood = $allprefs['squares'];
			$allprefs=unserialize(get_module_pref('allprefs','quarry'));
			$stone = $allprefs['blocks'];
		}

		$closed = $row['closed'];
		if($nextstar==7) $closed = 3;
		elseif($nextstar>7) $closed = floor($nextstar*0.6);
		if($nextstar>6)
			output("`n`n`$ Due to the severity of the improvement, your establishment will have to remain closed for the next %s days. You may use it at will, but no one else will be able to enter.",$closed);

		if($session['user']['gold']>=$costimpgold && $session['user']['gems']>=$costimpgems && $stone>=$starstone && $wood>=$starwood){
			output("`2`nDo you wish to purchase the next improvement?");
			
			addnav("Yes, add one star","runmodule.php?module=dwinns&op=add-star&dwid=$dwid");
		}elseif($session['user']['gold']<$costimpgold || $session['user']['gems']<$costimpgems){
			output("`2`nYou don't have enough gold or gems to purchase a new improvement for your establishment.");
		}
		else{
			output("`2`nYou don't have enough wood or stone to purchase a new improvement for your establishment.");
		}
	}else{
		output("`2`nYou already have all improvements.");
	}
	addnav(array("Back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");

?>
