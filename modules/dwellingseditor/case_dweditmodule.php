<?php
	page_header("Dwelling Listing");
	$dwid = httpget("dwid");
	addnav("Back to Dwelling List","runmodule.php?module=dwellingseditor");
	$mdule = httpget("mdule");
	if($mdule == ""){
		output("Select a pref to edit.");
	}else{
		if (httpget('sub') == "save") {
			// Save module prefs
			$post = httpallpost();
			reset($post);
			while(list($key, $val) = each($post)) {
				set_module_objpref("dwellings", $dwid, $key, $val, $mdule);
			}
			output("`^Saved!`0`n");
		}
		$link = "runmodule.php?module=dwellingseditor&op=dweditmodule&sub=save&dwid=$dwid&mdule=$mdule";
		require_once("lib/showform.php");
		rawoutput("<form action='$link' method='POST'>");
		module_objpref_edit("dwellings", $mdule, $dwid);
		rawoutput("</form>");
		addnav("",$link);
		//code from clan editor by CortalUX
	}
 	addnav("Module Prefs");
	module_editor_navs("prefs-dwellings","runmodule.php?module=dwellingseditor&op=dweditmodule&dwid=$dwid&mdule=");
?>