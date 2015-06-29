<?php
	require_once("lib/itemhandler.php");
/*	$constant = constant("HOOK_" . strtoupper($hookname));
	$item = db_prefix("item");
	$inventory = db_prefix("inventory");
	$sql = "SELECT $item.* FROM $item WHERE ($item.activationhook & $constant) != 0";
	$sql = "SELECT $item.*,
						(SUM($inventory.charges)+1)*COUNT($inventory.itemid) AS quantity
					FROM $item
					INNER JOIN $inventory ON $item.itemid = $inventory.itemid
					WHERE ($item.activationhook & $constant)
						AND inventory.userid = $acctid
					GROUP BY $item.itemid";*/

//	$result = db_query_cached($sql, "item-activation-$hookname");
	display_item_nav("forest", "forest.php");
?>