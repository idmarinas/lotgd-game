<?
set_module_pref('playerloc', $session['user']['location']);
if ($session['user']['loggedin'])
{
	//$session['user']['restorepage'] = "runmodule.php?module=jail&op=wakeup";<--ORIGINAL LINE RPGee.com
	$session['user']['restorepage'] = "village.php";
	$sql = "UPDATE " . db_prefix("accounts") . " SET loggedin=0, location='".translate_inline("`7The Jail")."', 
		restorepage='{$session['user']['restorepage']}' WHERE acctid = ".$session['user']['acctid'];
	db_query($sql);
	invalidatedatacache("charlisthomepage");
	invalidatedatacache("list.php-warsonline");
}
$session = array();
redirect("index.php");
?>