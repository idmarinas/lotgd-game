<?php
	global $REQUEST_URI;
	addnav("Inventario");
	addnav("Ver Inventario", "runmodule.php?module=inventory&user=".$args['acctid']."&login=".$args['login']."&return=".URLEncode($_SERVER['REQUEST_URI']));
?>