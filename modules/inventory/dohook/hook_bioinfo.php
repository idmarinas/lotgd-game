<?php
	global $REQUEST_URI;
	addnav("Inventory");
	addnav("View Inventory", "runmodule.php?module=inventory&user=".$args['acctid']."&login=".$args['login']."&return=".URLEncode($_SERVER['REQUEST_URI']));
?>