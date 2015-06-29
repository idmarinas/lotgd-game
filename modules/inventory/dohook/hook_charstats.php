<?php
	global $session;
	if (get_module_setting("withcharstats")==true && $session['user']['hitpoints']>0) {
		$open = translate_inline("Open Inventory");
		addnav("runmodule.php?module=inventory&op=charstat");
		addcharstat("Inventory");
		addcharstat("Inventory", "<center><a href='runmodule.php?module=inventory&op=charstat' target='inventory' onClick=\"".popup("runmodule.php?module=inventory&op=charstat").";return false;\">$open</a></center>");
	}
?>