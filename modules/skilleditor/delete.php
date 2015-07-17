<?php
	$sql = "DELETE FROM " . db_prefix("skills") . " WHERE id='$id'";
	db_query($sql);
	output("Item deleted!`n`n");
	redirect($from."op=editor&what=view");
	$op = "";
	httpset("op", $op);
?>
