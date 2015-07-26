<?php

page_header("Ability Management");

$op2 = httpget('op2');
if($op2 == "del"){
	$abi = ability_list();
	$key = httpget('key');
	$wert = $abi[$key];
	output("All values of the ability \"".$wert."\" were deleted.");
	$abil = db_prefix("abilities");
	db_query("DELETE FROM $abil WHERE ability='$key'");
	unset($abi[$key]);
	
	set_module_setting("abi",serialize($abi),"abi_basic");
	
	addnav("Continue");
	addnav("Continue","runmodule.php?module=abi_basic&op=superuser");
}
if($op2 == "new"){
	$new = httppost('ability');
	output("The ability \"$new\" has been added.");
	$abi = ability_list();
	$abi[] = $new;
	set_module_setting("abi",serialize($abi),"abi_basic");
	addnav("Continue");
	addnav("Continue","runmodule.php?module=abi_basic&op=superuser");
}
	
page_footer();

?>