<?
	$sql = "update ".db_prefix("module_userprefs")." set value=value+1 where value<>0 and setting='daysdeputy' and modulename='djail'";
	db_query($sql);

?>