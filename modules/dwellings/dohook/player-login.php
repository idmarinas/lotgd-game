<?php
	$page = $session['user']['restorepage'];
	if(substr($page,0,44) == "runmodule.php?module=dwellings&op=enter&dwid"){
		// BWAHAHAHAHACK!
		$grab = explode("=",$page);
		$dwid = $grab[count($grab)-1];
		invalidatedatacache("dwellings-sleepers-$dwid");
	}
?>