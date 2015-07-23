<?php
// Holy shit, heavy load.
if (get_module_pref("user_display")){
	$amulet = get_module_pref("amuletname");
	$ring = get_module_pref("ringname");
	$cloak = get_module_pref("cloakname");
	$glove = get_module_pref("glovename");
	$boot = get_module_pref("bootname");
	$helm = get_module_pref("helmname");
	$misc = get_module_pref("miscname");

	addcharstat("Equipment Info");
	if ( get_module_pref( 'helm' ) && $helm != 'None' && $helm != "" ) addcharstat("Helm", $helm);
	if ( get_module_pref( 'cloak' ) && $cloak != 'None' && $cloak != "") addcharstat("Cloak", $cloak);
	if ( get_module_pref( 'amulet' ) && $amulet != 'None' && $amulet != "") addcharstat("Amulet", $amulet);
	if ( get_module_pref( 'glove' ) && $glove != 'None' && $glove != "") addcharstat("Gloves", $glove);
	if ( get_module_pref( 'ring' ) && $ring != 'None' && $ring != "") addcharstat("Ring", $ring);
	if ( get_module_pref( 'boots' ) && $boot != 'None' && $boot != "") addcharstat("Boots", $boot);
	if ( get_module_pref( 'misc' ) && $misc != 'None' && $misc != "") addcharstat("Extra Item", $misc);
}
?>