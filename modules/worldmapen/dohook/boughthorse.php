<?php
//Give the player some starting fuel (or bring his horse to full strength, whatever you want to call it)
$id = $session['user']['hashorse'];
if ($id > 0) {
	$startingfuel = get_module_objpref("mounts",$id,"fuel");
	set_module_pref("fuel",$startingfuel);
}
?>