<?php
function dragoneggs_library15(){
	global $session;
	if (is_module_active("library")) $library="library";
	else{
		$library="dlibrary";
		output("`c`b`^Library`b`c`2`n");
	}
	page_header(array("%s Public Library",get_module_setting("libraryloc",$library)));
	$session['user']['armor']="Cool Jacket";
	$session['user']['armordef']+=2;
	$session['user']['defense']+=2;
	$session['user']['armorvalue']+=400;
	$session['user']['gold']-=800;
	output("`2You hand over the `^800 gold`2 and put on the Cool Jacket.  Sweet! It's `&2 defense points`2 better than your old armor.");
	debuglog("gained a Cool Jacket +2 to armor for 800 gold while researching dragon eggs at the Library.");
	addnav("Return to the Library","runmodule.php?module=$library&op=enter");
	villagenav();
}
?>