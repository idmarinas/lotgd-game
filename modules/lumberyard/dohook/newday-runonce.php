<?php
$cities = get_cities_id();

while($city = db_fetch_assoc($cities))
{
	$remainsize = get_module_objpref("city",$city['cityid'],"remainsize","lumberyard");
	$fullsize = get_module_objpref("city",$city['cityid'],"fullsize","lumberyard");
	$daygrowth = get_module_objpref("city",$city['cityid'],"daygrowth","lumberyard");
	$cutdown = get_module_objpref("city",$city['cityid'],"cutdown","lumberyard");
	$cutpercent = get_module_objpref("city",$city['cityid'],"cutpercent","lumberyard");
	$cccount = get_module_objpref("city",$city['cityid'],"cccount","lumberyard");
	$clearcut = get_module_objpref("city",$city['cityid'],"clearcut","lumberyard");
	$plantneed = get_module_objpref("city",$city['cityid'],"plantneed","lumberyard");
	
	//increase the forest size by the daily growth if the forest isn't quite full yet
	if ($remainsize<$fullsize-$daygrowth) 
		set_module_objpref("city", $city['cityid'], "remainsize", $remainsize+$daygrowth, "lumberyard");
		
	//set the wood price for the day
	$squarepay=(e_rand(get_module_setting("squarepaymin"),get_module_setting("squarepaymax")));
	set_module_setting("squarepay",$squarepay);
	
	//calculate the chance for a counter to trigger and if it triggers, increment the count if the forest isn't cutdown
	$cutchance=(e_rand(1,100));
	if ($cutchance<=$cutpercent && $cutdown==0)
		increment_module_objpref("city", $city['cityid'], "cccount", 1, "lumberyard");
	//if the counter is greater than the clearcut number, set the size to zero, the counter to zero, and the forest as cut down
	if ($cccount>=$clearcut) {
		set_module_objpref("city", $city['cityid'], "remainsize", 0, "lumberyard");
		set_module_objpref("city", $city['cityid'], "cutdown", 1, "lumberyard");
		set_module_objpref("city", $city['cityid'], "cccount", 0, "lumberyard");
	}
	//if the forest has naturally grown bigger than the need for planting then turn off the cutdown
	if ($cutdown==1 && $remainsize>$plantneed){
		set_module_objpref("city", $city['cityid'], "cutdown", 0, "lumberyard");
		set_module_objpref("city", $city['cityid'], "cccount", 0, "lumberyard");
	}
	//make sure the day's growth doesn't excede the maximum for the forest
	if($remainsize>$fullsize) 
		set_module_objpref("city", $city['cityid'], "remainsize", $fullsize, "lumberyard");
	
}

//reset the turns on newday-runonce if set for that
if (get_module_setting("runonce")) {
	$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
	$res = db_query($sql);
	while($row = db_fetch_assoc($res))
	{
		$allprefs=unserialize(get_module_pref('allprefs','lumberyard',$row['acctid']));
		$allprefs['usedlts']=0;
		$allprefs['squaresold']=0;
		set_module_pref('allprefs',serialize($allprefs),'lumberyard',$row['acctid']);
	}
}
?>