<?php
	global $session, $REQUEST_URI;
	addnav("Inventory");
	addnav("Show Inventory", "runmodule.php?module=inventory&user=".$session['user']['acctid']."&login=".$session['user']['login']."&return=".urlencode($_SERVER['REQUEST_URI']));
?>