<?php
$barrister		= get_module_pref("barrister");
$sql			= "SELECT name FROM ".db_prefix("accounts")." WHERE acctid = $barrister";
$result 		= db_query($sql) or die(db_error(LINK));
$row 			= db_fetch_assoc($result);
$barristername	= $row['name'];
output
(
	"`&You put on your finest attire for your trial. When you get there, %s `&prepares you for the trial, and lets you 
	know what to say and what not to say.", $playername
);
addnav("First witness", "runmodule.php?module=jail&op=firstwit");
?>