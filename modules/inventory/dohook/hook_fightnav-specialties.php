<?php
	require_once("lib/itemhandler.php");
//	$constant = HOOK_FIGHTNAV;
//	$item = db_prefix("item");
//	$inventory = db_prefix("inventory");
//	$sql = "SELECT $item.* FROM $item WHERE ($item.activationhook & $constant) != 0";
//	$result = db_query_cached($sql, "item-activation-$hookname");
	$args = display_item_fightnav($args);
?>