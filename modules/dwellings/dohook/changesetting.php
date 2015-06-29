<?php
	if ($args['setting'] == "villagename") {
		$sql = "UPDATE ".db_prefix("dwellings")." SET location='".addslashes($args['new'])."' WHERE location='".addslashes($args['old'])."'";
		db_query($sql);
	}
// Added this because of a new setting...
	if ($args['setting'] == "logoutlocation") {
		$sql = "UPDATE ".db_prefix("accounts")." SET location='".addslashes($args['new'])."' WHERE location='".addslashes($args['old'])."'";
		db_query($sql);
	}
?>