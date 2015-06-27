<?
	$sql = "UPDATE " . db_prefix("dwinns") . " SET closed=closed-1 WHERE closed>0";
	db_query($sql);
?>
