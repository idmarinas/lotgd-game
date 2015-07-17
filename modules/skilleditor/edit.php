<?php
	$sql = "SELECT * FROM " . db_prefix("skills") . " WHERE id='$id'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	rawoutput("<form action='runmodule.php?module=skilleditor&op=editor&what=save&id=$id' method='POST'>");
	addnav("","runmodule.php?module=skilleditor&op=editor&what=save&id=$id");
	require_once("lib/showform.php");
	showform($skillarray,$row);
	rawoutput("</form>");
?>
