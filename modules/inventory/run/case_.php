<?php
	$user = httpget('user');
	$login = httpget('login');
	$return = httpget('return');
	$return = cmd_sanitize($return);
	$return = substr($return,strrpos($return,"/")+1);
	page_header("%s's Inventory ", $login);
	require_once("lib/villagenav.php");
	show_inventory($user);
	tlschema("nav");
	addnav("Return whence you came",$return);
	tlschema();
	villagenav();
	page_footer();
?>