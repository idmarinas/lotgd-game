<?
$witness2		= get_module_pref("witness2");
$sql			= "SELECT name FROM ".db_prefix("accounts")." WHERE acctid =$witness2"; 
$result 		= db_query($sql) or die(db_error(LINK)); 
$row 			= db_fetch_assoc($result); 
$witness2name	= $row['name']; 
output
(
	"%s `&Takes the stand and tells of all the good things you have done for the village. Mention is also made as to 
	how much you have given to the chapel.`n", $witness2name
); 
addnav("Wait for a verdict", "runmodule.php?module=jail&op=verdict"); 
?>