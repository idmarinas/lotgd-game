<?
	output("`4Un-Installing dwellings Module: dwinns.`n");
	$sql = "DROP TABLE ".db_prefix("dwinns");
	db_query($sql);
	$sql = "DELETE FROM ".db_prefix("dwellingtypes")." WHERE module='dwinns'";
	db_query($sql);
?>