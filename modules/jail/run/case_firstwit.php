<?
$witness1		= get_module_pref("witness1");
$sql			= "SELECT name FROM ".db_prefix("accounts")." WHERE acctid =$witness1";
$result 		= db_query($sql) or die(db_error(LINK));
$row 			= db_fetch_assoc($result);
$witness1name	= $row['name'];
output
(
	"%s `& takes the stand. Many questions are asked about the events leading up to your capture. You are unsure how well 
	it went.", $witness1name
);
addnav("Second witness", "runmodule.php?module=jail&op=secwit");
?>