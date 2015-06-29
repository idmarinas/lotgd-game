<?php
	$item = db_prefix("item");
	$inventory = db_prefix("inventory");
	$itembuffs = db_prefix("itembuffs");
	$sql = "DROP TABLE $item, $inventory, $itembuffs";
?>