<?
	$dwid = httpget("dwid");
	page_header("Purchasing an improvement");
	
	$multimpgold = get_module_setting("multimpgold","dwinns");
	$multimpgems = get_module_setting("multimpgems","dwinns");
	$basisprice = get_module_setting("basisprice","dwinns");
	
	$sql = "SELECT stars,closed FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	$nextstar = $row['stars']+1;
	$costimpgold = $multimpgold * $nextstar;
	$costimpgems = $multimpgems * $nextstar;
	$closed = $row['closed'];
	if($nextstar==7) $closed += 3;
	elseif($nextstar>7) $closed += floor($nextstar*0.6);
	$starwood = 0;
	$starstone = 0;
	$wood = 0;
	$stone = 0;
	if (is_module_active('lumberyard')){
		$starwood = $nextstar;
		$allprefs=unserialize(get_module_pref('allprefs','lumberyard'));
		$allprefs['squares']-=$starwood;
		set_module_pref("allprefs",serialize($allprefs),"lumberyard");
	}
	if (is_module_active('quarry')){
		$starstone = $nextstar;
		$allprefs=unserialize(get_module_pref('allprefs','quarry'));
		$allprefs['blocks']-=$starstone;
		set_module_pref("allprefs",serialize($allprefs),"quarry");
	}
	
	$session['user']['gold']-=$costimpgold;
	$session['user']['gems']-=$costimpgems;
	set_module_pref("blocks",$stone-$starstone,"quarry");
	
	output("`2You add a new improvement to your establishment.`n");
	
	$sql = "UPDATE " . db_prefix("dwinns") . " SET stars='$nextstar', price=price+($basisprice/2),closed=$closed WHERE dwid='$dwid'";
	db_query($sql);
	$sql2 = "UPDATE " . db_prefix("dwellings") . " SET goldvalue=goldvalue+round($costimpgold*0.3),gemvalue=gemvalue+round($costimpgems*0.3) WHERE dwid='$dwid'";
	db_query($sql2);
	
	output("`2`nYour establishment now has %s stars. You just purchased ",$nextstar);
	switch($nextstar){
		case "1": output("`&some cats."); break;
		case "2": output("`&a half-decent cook."); break;
		case "3": output("`&soft beds."); break;
		case "4": output("`&a jukebox."); break;
		case "5": output("`&room service."); break;
		case "6": output("`&very short skirts for the maids."); break;
		case "7": output("`&marble floors."); break;
		case "8": output("`&an indoor water fountain."); break;
		case "9": output("`&an indoor pool."); break;
		case "10": output("`&an overpriced wooden sauna."); break;
	}
	debuglog("purchased star for dwelling $dwid");
	
	if($nextstar>6)
			output("`n`n`$ Due to the severity of the improvement, your establishment will have to remain closed for the next %s days. You may use it at will, but no one else will be able to enter.",$closed);
	$nextstar++;	
	if($nextstar < 11){
		$costimpgold = $multimpgold * $nextstar;
		$costimpgems = $multimpgems * $nextstar;
		$starwood = 0;
		$starstone = 0;
		$wood = 0;
		$stone = 0;
		if (is_module_active('lumberyard')){
			$starwood = $nextstar;
			$allprefs=unserialize(get_module_pref('allprefs','lumberyard'));
			$wood = $allprefs['squares'];
		}
		if (is_module_active('quarry')){
			$starstone = $nextstar;
			$allprefs=unserialize(get_module_pref('allprefs','quarry'));
			$stone = $allprefs['blocks'];
		}
		output("`2`n`nPurchasing improvement nr. %s will cost you `6%s gold`2 and `4%s gems`2.`n",$nextstar,$costimpgold,$costimpgems);
		if($starwood>0 || $starstone>0)
			output("`2Additionaly, this improvement will cost you `Q%s wood`2 and `7%s stone`2 for a large number of small little things (e.g. a house for the cat, renovations in the kitchen, a pair of new beds, etc).`n",$starwood,$starstone);
	
		if($session['user']['gold']>=$costimpgold && $session['user']['gems']>=$costimpgems && $stone>=$starstone && $wood>=$starwood){
			output("`2`nDo you wish to purchase more improvements?");
			
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
	addnav(array("Back to the %s",get_module_setting("dwname","dwinns")),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>
