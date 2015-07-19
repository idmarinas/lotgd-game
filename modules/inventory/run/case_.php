<?php
	$user = httpget('user');
	$login = httpget('login');
	$return = httpget('return');
	$return = cmd_sanitize($return);
	$return = substr($return,strrpos($return,"/")+1);
	page_header("Inventario de %s", $login);
	require_once("lib/villagenav.php");
	show_inventory($user);
	tlschema("nav");
	addnav("Volver por donde has venido",$return);
	tlschema();
	villagenav();
	page_footer();
?>