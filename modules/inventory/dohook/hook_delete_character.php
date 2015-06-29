<?php
	$inventory = db_prefix("inventory");
	$sql = "DELETE FROM $inventory WHERE userid = " . $args['acctid'];
	db_query($sql);
?>