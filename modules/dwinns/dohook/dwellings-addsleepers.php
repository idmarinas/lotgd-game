<?php
if($args['type']=="dwinns"){
	if(httpget("rent")=="true"){
		$sql = "SELECT rooms FROM " . db_prefix("dwinns") . " WHERE dwid = " . $args['dwid'];
		$result = db_query($sql);
		$row  = db_fetch_assoc($result);
		db_free_result($result);
		$args['allowed']+=$row['rooms'];
	}else{
		
		$sql = "SELECT guests FROM " . db_prefix("dwinns") . " WHERE dwid = " . $args['dwid'];
		$result = db_query($sql);
		$row  = db_fetch_assoc($result);
		$args['allowed']+=$row['guests'];
	}
}
?>