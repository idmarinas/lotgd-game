<?php
$count=0;
$array=array("","heal","bank","uni","inn","witch","hof","police","weapons","armor","diner","gypsy","heidi","library","jewelry","tattoo","magic","animal","gardens","rock","church","news","docks","bath");
if ($session['user']['dragonkills']>=get_module_setting("mindk")){
	for ($i=1;$i<=23;$i++) {
		$loc=$array[$i];
		if (get_module_setting($loc."lodge")>0) $count++;
	}
	if ($count>0){
		$args['count']++;
		$format = $args['format'];
		$str = translate("You may need to purchase a Dragon Egg Research Pass through the lodge to look for eggs at certain locations. A pass to each location lasts until you kill your next Dragon Kill. Point costs may vary based on location.");
		output($format, $str, true);
	}
}
?>