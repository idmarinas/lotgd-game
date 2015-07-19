<?php
	require_once("lib/itemhandler.php");
	$skill = httpget('skill');
	if ($skill=="ITEM"){
		$itemid = (int)httpget('l');
		$invid = httpget('invid');
		$item = get_inventory_item_by_id($itemid, $invid);
		require_once("lib/buffs.php");
		if ($item['buffid'] > 0) {
			apply_buff($item['name'], get_buff($item['buffid']));
		}
		if ($item['execvalue'] > "") {
			require_once("lib/itemeffects.php");
			if ($item['exectext'] > "") {
				output($item['exectext'], $item['name']);
			} else {
				output("You activate %s!", $item['name']);
			}
			output_notl("%s`n", get_effect($item, $item['noeffecttext']));
		}
		if ($item['charges'] > 1) {
			uncharge_item((int)$itemid, false, $invid);
		} else if (isset($item['invid'])) {
			remove_item((int)$itemid, 1, false, $invid);
		} else {
			remove_item((int)$itemid, 1);
		}
	}
?>