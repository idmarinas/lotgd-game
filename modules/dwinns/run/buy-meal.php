<?
	$dwid = httpget("dwid");
	page_header("Eating a meal");
		
	if($session['user']['hitpoints'] < $session['user']['maxhitpoints']){
		output("`2You eat a nourishing meal and replenish any lost health points.`n`n");
		$loglev = log($session['user']['level']);
		$cost = round(($loglev * ($session['user']['maxhitpoints']-$session['user']['hitpoints'])) + ($loglev*10),0);
		$result=modulehook("healmultiply",array("alterpct"=>1.0));
		$cost*=$result['alterpct'];
		$session['user']['hitpoints']=$session['user']['maxhitpoints'];
	}else{
		output("`2You eat a nourishing meal, which gives you more energy to fight creatures.`n`n");
		
		$sql = "SELECT stars FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		
		$cost = ($session['user']['level'] + $row['stars']) * (120 - (2 * $row['stars']));
		$session['user']['hitpoints']+= $session['user']['level'] + $row['stars'];
		
		$meals=get_module_pref("dwinnsmeals","dwinns");
		$meals++;
		set_module_pref("dwinnsmeals",$meals,"dwinns");
	}
	$session['user']['gold']-=$cost;
	
	$sql = "UPDATE " . db_prefix(dwinns) . " SET logmeals=logmeals+$cost, meals=meals-1, statmealsprofit=statmealsprofit+$cost, statmeals=statmeals+1 WHERE dwid='$dwid'";
	db_query($sql);
	
	addnav(array("Back to the %s",get_module_setting("dwname","dwinns")),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>
