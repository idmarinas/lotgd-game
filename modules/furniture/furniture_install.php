<?php
	module_addhook("changesetting");
	module_addhook("newday");
	module_addhook("newday-runonce");
	module_addhook("dragonkill");
	module_addhook("dwellings-sold");
	module_addhook("dwellings-inside");
	module_addhook("dwellings");
	$sql1="select module from ".db_prefix("dwellingtypes")."";
	$result1 = db_query($sql1);
	for ($i = 0; $i < db_num_rows($result1); $i++){ 
		$j=$i+1;
		$row1 = db_fetch_assoc($result1);
		$module=$row1['module'];
		if($j<=7) set_module_setting("modulename".$j,$module);
		else set_module_setting("modulename8",1);
	}
?>