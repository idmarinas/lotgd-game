<?
	$dwid = httpget("dwid");
	$dwname = get_module_setting("dwname","dwinns");
	page_header("Sitting at the table");
	
	$sql = "SELECT name FROM " . db_prefix("dwellings") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$name = $row['name'];
	$sql = "SELECT meals, stars FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	output("`2At %s`2 you can eat a nourishing meal that will replenish any lost health points you may have lost during the day.`n`n",$name);
	output("`2By purchasing a meal with full health in an %s`2 with at least one star you'll add a few extra health points, but these will get very expensive, very fast.`n`n",$dwname);

	if($row['meals'] > 0 && get_module_pref("dwinnsmeals")<get_module_setting("maxdwinnsmeals")){
		if($session['user']['hitpoints'] < $session['user']['maxhitpoints']){
			$loglev = log($session['user']['level']);
			$cost = ($loglev * ($session['user']['maxhitpoints']-$session['user']['hitpoints'])) + ($loglev*10);
			$cost = round($cost,0);
			$result=modulehook("healmultiply",array("alterpct"=>1.0));
			$cost*=$result['alterpct'];
			output("`2Purchasing a meal will cost you `6%s gold`2.`n",$cost);
			if($session['user']['gold'] >= $cost){
				addnav("Eat a meal");
				addnav(array("Eat a meal"),"runmodule.php?module=dwinns&op=buy-meal&dwid=$dwid");
			}else
				output("`2`nYou don't have enough gold to purchase a meal.");
		}elseif($row['stars'] > 0){
			$cost = ($session['user']['level'] + $row['stars']) * (120 - (2 * $row['stars']));
			output("`2Purchasing a meal will cost you `6%s gold`2.`n",$cost);
			if($session['user']['gold'] >= $cost){
				addnav("Eat a meal");
				addnav(array("Eat a meal"),"runmodule.php?module=dwinns&op=buy-meal&dwid=$dwid");
			}else
				output("`2`nYou don't have enough gold to purchase a meal.");
		}else
			output("`2The crap they serve here, wont do you any good.");
	}elseif($row['meals'] == 0)
		output("`2Sadly, there are no meals prepared");
	elseif(get_module_pref("dwinnsmeals")>=get_module_setting("maxdwinnsmeals"))
		output("`$ You *burp* couldn't eat another *burp* bite today, or you would barf...");
	
	addnav(array("Back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>
