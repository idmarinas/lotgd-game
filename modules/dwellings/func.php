<?php

function get_typeid($modulename){
	$sql = "SELECT typeid FROM ".db_prefix("dwellingtypes")." WHERE module='$modulename'";
	$res = db_query($sql);
	$row = db_fetch_assoc($res);
	return $row['typeid'];
}

?>