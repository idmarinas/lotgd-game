<?php
output("Who will you defend?`n");
addnav("Back to jail", "runmodule.php?module=jail");
$name		= translate_inline("Name");
$level	= translate_inline("Level");
$write	= translate_inline("Write mail");
$repr		= translate_inline("Represent");
output
(
	"<table border='0' cellpadding='3' cellspacing='0'><tr class='trhead'><td style=\"width:350px\">$name</td>
	<td style=\"width:150px\">$level</td><td>&nbsp;</td></tr>",true
);
$killsneeded		= $session['user']['dragonkills'] -$bardk;
$accounts 			= db_prefix("accounts");
$module_userprefs 	= db_prefix("module_userprefs");
$sql 				= "SELECT $accounts.name AS name,$accounts.level AS level,$accounts.login AS login,$accounts.acctid AS acctid, 
	$module_userprefs.userid FROM $module_userprefs INNER JOIN $accounts ON $accounts.acctid = $module_userprefs.userid 
	WHERE $module_userprefs.setting = 'injail' AND $module_userprefs.value > 0 AND $accounts.dragonkills < $killsneeded ORDER BY 
	$accounts.level DESC";
$result = db_query($sql) or die(db_error(LINK));
for ($i = 0 ; $i < db_num_rows($result) ; $i++)
{
	$row = db_fetch_assoc($result);
	output("<tr class='".($i%2?"trlight":"trdark")."'><td>", true);
	output
	("
		<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup
		("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' 
		width='16' height='16' alt='$write' border='0'></a>",true
	);
	output
	(
		"".$row['name']."</a></td><td>`^".$row['level']."`7</td><td>[<a href='runmodule.php?module=jail&op=barrep&player=".
		rawurlencode($row['acctid'])."'>$repr</a> ]</td></tr>",true);addnav("","bio.php?char=".rawurlencode($row['login']).""
	);
	addnav("", "runmodule.php?module=jail&op=barrep&player=".rawurlencode($row['acctid'])."");
} 
output("</table>", true);
?>