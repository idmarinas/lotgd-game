<?php
	require_once("lib/itemhandler.php");
	$inventory = get_inventory();
	$count=0;
	$removeables = array();
	while($item = db_fetch_assoc($inventory)) {
		if ($item['loosechance'] == 100) {
			$removeables[$item['itemid']] = $item['quantity'];
		} else if($item['loosechance'] == 0) {
		}
	}
	if ($count == 1) {
		output("`n`\$Uno de tus objetos fue dañado durante la lucha. ");
	} else if ($count > 1) {
		output("`n`\$Un total de `^%s`\$ de tus objetos fueron dañados durante la lucha.", $count);
	}
?>