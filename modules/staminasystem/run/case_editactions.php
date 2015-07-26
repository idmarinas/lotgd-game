<?php

page_header("Actions Management");

$op2 = httpget('op2');
if($op2 == "del"){
	$act = actions_list();
	$key = httpget('key');
	$value = $act[$key];
	output("All values of the action \"".$value."\" were deleted.");
	$stamina = db_prefix("stamina");
	db_query("DELETE FROM $stamina WHERE action='$key'");
	unset($act[$key]);
	
	set_module_setting("actionsarray",serialize($act),"staminasystem");
	actions_list();
	$actiondebug = get_module_setting("actionsarray","staminasystem");
	debug($actiondebug);
	addnav("Continue");
	addnav("Continue","runmodule.php?module=staminasystem&op=superuser");
}
if($op2 == "new"){
	$new = httppost('action');
	output("The action \"$new\" has been added.");
	$act = actions_list();
	$act[] = $new;
	set_module_setting("actionsarray",serialize($act),"staminasystem");
	$act = actions_list();
	addnav("Continue");
	addnav("Continue","runmodule.php?module=staminasystem&op=superuser");
}
	
page_footer();

?>