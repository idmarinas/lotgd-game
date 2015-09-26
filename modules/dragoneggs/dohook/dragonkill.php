<?php
	set_module_pref("researches",0);
	set_module_pref("retainer",0);
	set_module_pref("rumor",0);
	set_module_pref("quest1",0);
	set_module_pref("progress",0);
	set_module_pref("exchange",0);
	set_module_pref("puzzlepiece",0);
	if (get_module_pref("lantern")==1) set_module_pref("lantern",2);
	$array=array("","heal","bank","uni","inn","witch","hof","police","weapons","armor","diner","gypsy","heidi","library","jewelry","tattoo","magic","animal","gardens","rock","church","news","docks","bath");
	for ($i=1;$i<=23;$i++) {
		$loc=$array[$i];
		if (get_module_pref($loc."access")>0) set_module_pref($loc."access",0);
	}
?>