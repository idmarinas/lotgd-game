<?
	$dwid = httpget("dwid");
	$rounds = httppost("rounds");
	$attack = httppost("attack");
	$defense = httppost("defense");
	page_header("Customizing your ale");
	
	if($rounds=="")
		$rounds=0;
	if($attack=="")
		$attack=0;
	if($defense=="")
		$defense=0;
	
	addnav(array("Back to the %s",get_module_setting("dwname","dwinns")),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	
	$maxtotal = get_module_setting("maxalepoints");
	$maxeach = get_module_setting("maxalepointseach");
	if($rounds+$attack+$defense > $maxtotal){
		output("`2You exceeded the maximum total of %s points`n", $maxtotal);
		addnav("Retry","runmodule.php?module=dwinns&op=change-ale&dwid=$dwid");
	}elseif($rounds > $maxeach || $rounds < 0 || $attack > $maxeach || $attack < 0 || $defense > $maxeach || $defense < 0){
		output("`2One of your attributes is out of bounds!`n");
		addnav("Retry","runmodule.php?module=dwinns&op=change-ale&dwid=$dwid");
	}else{
		output("`2Congratulations, you tweaked the recipe of you ale a bit.`n`n");
		output("`&Rounds:  %s`n`n", $rounds);
		output("`&Attack:  %s`n`n", $attack);
		output("`&Defense: %s`n`n", $defense);
		
		$sql = "UPDATE " . db_prefix("dwinns") . " SET alerounds='$rounds', aleattack='$attack', aledefense='$defense' WHERE dwid='$dwid'";
		db_query($sql);
		
		if($rounds+$attack+$defense < $maxtotal){
			output("`2You didn't use all of your points, you might want to try again.`n");
			addnav("Retry","runmodule.php?module=dwinns&op=change-ale&dwid=$dwid");
		}
	}
?>