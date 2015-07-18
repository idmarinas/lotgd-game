<?php
	$skills = db_prefix("skills");
	$skillsbuffs = db_prefix("skillsbuffs");
	$sql = "DROP TABLE $skills, $skillsbuffs";
	db_query($sql);
?>