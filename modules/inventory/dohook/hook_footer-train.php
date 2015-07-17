<?php
	if (httpget('op') == '') {
		require_once("lib/itemhandler.php");
		display_item_nav("train", "train.php");
	}
?>