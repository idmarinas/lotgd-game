<?php
	$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='fishmap' and modulename='docks'";
	db_query($sql);
	$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='fishingtoday' and modulename='docks'";
	db_query($sql);

?>