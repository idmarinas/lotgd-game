<?php
	$dwid = httpget('dwid');
	$confirm = httpget("confirm");
	if($confirm){
		output("This dwelling has been deleted.");
		$sql = "DELETE FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
		db_query($sql);
		$sql = "DELETE FROM ".db_prefix("dwellingkeys")." WHERE dwid=$dwid";
		db_query($sql);
	}else{
		addnav("Are you sure?");
		addnav("Yes","runmodule.php?module=dwellingseditor&op=delete&dwid=$dwid&confirm=1");
		output("Are you sure you want to delete this dwelling?");
	}
?>