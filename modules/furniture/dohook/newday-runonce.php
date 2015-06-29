<?php
	if (get_module_setting("resetnd")==1){
		$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='usedbed' and modulename='furniture'";
		db_query($sql);
		$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='usedtable' and modulename='furniture'";
		db_query($sql);				
		$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='usedchair' and modulename='furniture'";
		db_query($sql);				
	}
?>