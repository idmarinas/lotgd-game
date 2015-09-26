<?php
	global $session;
	$op = httpget('op');
	$op2 = httpget('op2');
	$op3 = httpget('op3');
	$op4 = httpget('op4');
	require_once("modules/docks/docks_func.php");
	page_header("The Docks");
	$knownmonsters = array('fishermanfight','fishcrew','fishshark');
	if (in_array($op, $knownmonsters) || $op == "fight") {
		docks_fight($op);
		die;
	}
if ($op=="docks") {
	require_once("modules/docks/docks_docks.php");
	docks_docks();
}
if ($op=="fishingexpedition"){
	require_once("modules/docks/docks_fishingexpedition.php");
	docks_fishingexpedition();
}
if ($op=="fishingexpeditiona"){
	require_once("modules/docks/docks_fishingexpeditiona.php");
	docks_fishingexpeditiona();
}
page_footer();
?>