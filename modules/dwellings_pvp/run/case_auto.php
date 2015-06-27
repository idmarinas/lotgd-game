<?php
	$act = httpget('act');
	set_module_objpref("dwellings",$dwid,"isauto",$act,"dwellings_pvp");
	redirect("runmodule.php?module=dwellings&op=manage&dwid=$dwid");
?>