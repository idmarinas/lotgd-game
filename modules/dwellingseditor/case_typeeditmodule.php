<?php
	$mdule = httpget("mdule");
	if($mdule == ""){
		output("Select a pref to edit.");
		addnav("Operations");
		$sql = "SELECT module FROM ".db_prefix("dwellingtypes")." WHERE typeid=$typeid";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		$modlink = $row['module'];
		if ($session['user']['superuser'] & SU_EDIT_CONFIG)
			addnav(array("%s Module Settings",$modlink),"configuration.php?op=modulesettings&module=$modlink");
	}else{
		if (httpget('sub') == "save") {
			// Save module prefs
			$post = httpallpost();
			reset($post);
			while(list($key, $val) = each($post)) {
				set_module_objpref("dwellingtypes", $typeid, $key, $val, $mdule);
			}
			output("`^Saved!`0`n");
		}
		$link = "runmodule.php?module=dwellingseditor&op=typeeditmodule&sub=save&typeid=$typeid&mdule=$mdule";
		require_once("lib/showform.php");
		rawoutput("<form action='$link' method='POST'>");
		module_objpref_edit("dwellingtypes", $mdule, $typeid);
		rawoutput("</form>");
		addnav("",$link);
		//code from clan editor by CortalUX
	}
 	addnav("Module Prefs");
module_editor_navs("prefs-dwellingtypes","runmodule.php?module=dwellingseditor&op=typeeditmodule&typeid=$typeid&mdule=");
?>