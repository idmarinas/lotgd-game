<?php
$cities = get_cities_id();

if (!get_module_setting("runonce")) {
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['usedlts']=0;
	$allprefs['squaresold']=0;
	set_module_pref('allprefs',serialize($allprefs));	
}

output_notl('`n');
while($city = db_fetch_assoc($cities))
{
	$remainsize = get_module_objpref("city",$city['cityid'],"remainsize","lumberyard");
	$cutdown = get_module_objpref("city",$city['cityid'],"cutdown","lumberyard");
	
	if ($remainsize==0) output("`@There`6 aren't any trees");
	elseif ($remainsize==1) output("`@There `6is one tree");
	else output("`@There are `6%s trees",$remainsize);
	output("`@ in the forest of `i%s`i that can be harvested in `b`QT`qhe `QL`qumber `QY`qard`b`@.`n", $city['cityname']);
	if ($cutdown==0){
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['ccspiel']=0;
		set_module_pref('allprefs',serialize($allprefs));	
	}	
}
output_notl('`n');
?>