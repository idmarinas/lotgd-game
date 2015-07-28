<?
	$dwid = httpget("dwid");
	page_header("Drinking");
	
	$sql = "SELECT drinkqual, brewexp, brewname, alerounds, aleattack, aledefense FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	$drinkqual = $row['drinkqual'];
	$bexp = floor(($row['brewexp'])/1000)+1;
	$brewprice = 10 * ($bexp + $drinkqual) * $session['user']['level'];	
	$session['user']['gold']-=$brewprice;
	
	output("`2You drink a frosty ale and seem to win enough courage to draw your weapon and fight everything in your way.`n`n");
	
	if($row['alerounds']==0)
		$rounds=1;
	else
		$rounds = ($row['alerounds'] * 2) * ($bexp + $drinkqual);
	
	apply_buff('User Inn Ale',
		array(
			"name"=>array("`5%s",$row['brewname']),
			"rounds"=>$rounds,
			"defmod"=>(($row['aledefense'] / 100) * ($bexp + $drinkqual)) + 0.95,
			"atkmod"=>(($row['aleattack'] / 50) * ($bexp + $drinkqual)) + 0.9,
			"roundmsg"=>array("`2The %s`2 you drank gives you enough courage to take on the whole world. Attack!!!",$row['brewname']),
			"schema"=>"module-dwinns",
		)
	);
	
	$sql = "UPDATE " . db_prefix("dwinns") . " SET logdrinks=logdrinks+$brewprice, drinks=drinks-1, statdrinksprofit=statdrinksprofit+$brewprice, statdrinks=statdrinks+1 WHERE dwid='$dwid'";
	db_query($sql);
		
	$drunk = get_module_pref("drunkeness","drinks");
	if($drunk < 0 || $drunk == "")
		$drunk = 0;
	$drunk += get_module_setting("aledrunk");
	set_module_pref("drunkeness",$drunk,"drinks");
	set_module_pref("harddrinks",get_module_pref("harddrinks","drinks")+1,"drinks");
	
	addnav(array("Back to the %s",get_module_setting("dwname","dwinns")),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>
