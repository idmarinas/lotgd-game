<?php
	global $session;
	$id = httpget('id');
	$item = get_inventory_item((int)$id);
	$return = httpget('return');
	$return = cmd_sanitize($return);
	if (httpget('returnhandle') != 1) {
		$return = substr($return,strrpos($return,"/")+1);
	}
	if (strpos($return, "forest.php") !== false) $return= "forest.php";
	if (strpos($return, "village.php") !== false) $return= "village.php";
	if ($item['charges'] > 1) {
		uncharge_item((int)$id, false, $item['invid']);
	} else if (isset($item['invid'])) {
		remove_item((int)$id, 1, false, $item['invid']);
	} else {
		remove_item((int)$id, 1);
	}
	require_once("lib/buffs.php");
	if ($item['buffid'] <> 0)
		apply_buff($item['name'], get_buff($item['buffid']));
	if ($item['execvalue'] > "") {
		page_header();
		if ($item['exectext'] > "") {
			output($item['exectext'], $item['name']);
		} else {
			output("You activate %s!", $item['name']);
		}
		require_once("lib/itemeffects.php");
		$text = get_effect($item, $item['noeffecttext']);
		output_notl("`n`n%s", $text);
		if ($session['user']['hitpoints'] <= 0 || $session['user']['alive'] == false) {
			addnav("Return");
			addnav("Daily News", "news.php");
		} else {
			display_item_nav(httpget('hookname'), $return);
			addnav("Return");
			addnav("Return whence you came", $return);
		}
		page_footer();
	} else {
		redirect($return);
	}
?>
