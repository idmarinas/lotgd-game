<?
$dwid = httpget("dwid");
$quant = httpget("quant");
page_header("Brewing Ale");

$sql = "SELECT drinks, brewexp FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
$result = db_query($sql);
$row = db_fetch_assoc($result);

$drinks = $row['drinks'] + $quant;
$brewexp = $row['brewexp'] + $quant;
$session['user']['turns']-=$quant;
$bexp = min(floor(($brewexp)/1000),10);

output("`2You brew `5%s liters of ale`2. This takes you the same amount of turns until it's brewed, aged, bottled and corked.`n",$quant);
output("`2`nYou now have `5%s liters of ale`2 ready to be served.`n`n",$drinks);
debuglog("brewed $quant liters of ale for dwelling $dwid");
output("`2In this brewery `5%s liters of ale`2 have been brewed until today. The currenct experience of the brewer is `0%s`2, ",$brewexp,$bexp);
if($bexp < 10){
	$expneed = ($bexp+1) * 1000 - $brewexp;
	output("and will improve after brewing further `5%s liters of ale`2.",$expneed);
}

$sql = "UPDATE " . db_prefix("dwinns") . " SET drinks='$drinks', brewexp='$brewexp' WHERE dwid='$dwid'";
db_query($sql);

addnav("Customize your Ale","runmodule.php?module=dwinns&op=change-ale&dwid=$dwid");
if($session['user']['turns']>0){
	output("`2`nDo you wish to brew more ale?");
	addnav("Yes, brew one liter","runmodule.php?module=dwinns&op=brew-ale&dwid=$dwid&quant=1");
	if($session['user']['turns']>4){
		addnav("Yes, brew five liters","runmodule.php?module=dwinns&op=brew-ale&dwid=$dwid&quant=5");
		if($session['user']['turns']>9)
			addnav("Yes, brew ten liters","runmodule.php?module=dwinns&op=brew-ale&dwid=$dwid&quant=10");
	}
}

addnav(array("No, back to the %s",get_module_setting("dwname","dwinns")),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>
