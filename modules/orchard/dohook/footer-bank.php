<?php
	$allprefs=unserialize(get_module_pref('allprefs'));
	if ($allprefs['seed']==11 && $allprefs['bankkey']==2) addnav("Lock Box Withdrawal","runmodule.php?module=orchard&op=bank");
?>