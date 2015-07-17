<?php
// We only care about the names of locations.
if ($args['setting'] == "villagename") { 
	$old = $args['old'];
	$new = $args['new'];
	// Handle any locations of the old name and convert them.
	$x = get_module_setting($old.'X');
	$y = get_module_setting($old.'Y');
	$z = get_module_setting($old.'Z');
	set_module_setting('worldmapen'.$new.'X', $x);
	set_module_setting('worldmapen'.$new.'Y', $y);
	set_module_setting('worldmapen'.$new.'Z', $z);
	set_module_setting('worldmapen'.$old.'X', "");
	set_module_setting('worldmapen'.$old.'Y', "");
	set_module_setting('worldmapen'.$old.'Z', "");
	// Handle any players who last city was the old name.
	$sql = "UPDATE " . db_prefix("module_userprefs") . " SET value='".addslashes($new) . "' WHERE value='".addslashes($old) ."' AND modulename='worldmapen' AND setting = 'lastCity'";
	db_query($sql);
}
?>