<?php
	require_once("lib/itemhandler.php");
/*	$constant = constant("HOOK_" . strtoupper($hookname));
	$item = db_prefix("item");
	$inventory = db_prefix("inventory");
	$sql = "SELECT $item.* FROM $item WHERE ($item.activationhook & $constant) != 0";
	$result = db_query_cached($sql, "item-activation-$hookname");*/
	display_item_nav("village", "village.php");
?>