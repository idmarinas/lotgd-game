<?
	$dwname = get_module_setting("dwname","dwinns");
	$dwid = httpget("dwid");
	page_header("Sitting at the bar");
	
	$sql = "SELECT drinks, drinkqual, brewname, brewexp FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	$alename = $row['brewname'];
	$bexp = min(floor(($row['brewexp'])/1000),10)+1;
		
	if ($session['user']['sex']==SEX_MALE)
		output("`2This %s`2 is known for its %s`2. A nice cold frosted glas of it seems just about the right stuff to put hairs on your chest before you go creature killing.`n`n",$dwname,$alename);
	else
		output("`2The stench of smelly men drinking %s`2 and spitting on the floors makes your nails grow. What you need now is a nice cold one to wash away the taste of unwashed testosterone.`n`n",$alename);
	
	$brewprice = 10 * ($bexp + $row['drinkqual']) * $session['user']['level'];
	
	$drunk = get_module_pref("drunkeness","drinks");
	if($drunk < 0 || $drunk == "")
		$drunk = 0;
	
	output("`2Purchasing a %s`2 will cost you `6%s gold`2.`n",$alename,$brewprice);
	if($session['user']['gold'] < $brewprice)
		output("`2`nYou don't have enough gold to purchase a drink.");
	else{
		if($row['drinks'] > 0){
			if(!is_module_active("drinks") || (get_module_pref('harddrinks',"drinks") < get_module_setting('hardlimit',"drinks") && $drunk < get_module_setting("maxdrunk","drinks"))){
				addnav("Order a drink");
				addnav(array("Order a drink"),"runmodule.php?module=dwinns&op=buy-drink&dwid=$dwid");
			}else
				output("`2You're far too drunk to reach the bar, lifting a keg and placing it near your mouth is an impossible dream");
		}else
			output("`2Sadly, there are no drinks available`n`n");
	}
	addnav(array("Back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
?>