<?php
//Superuser-Addon
//Anzeigen ALLER installierten Modulen

require_once("lib/superusernav.php");

function allmodulegroup_getmoduleinfo(){
	$info = array(
	"name"=>"All Module Group - Modulemanager",
	"version"=>"0.1",
		"author"=>"`2R`@o`ghe`2n `Qvon `2Fa`@lk`genbr`@uch`0",
		"category"=>"Administrative",
		"download"=>"http://www.lotgd.de/downloads/allmodulegroup.zip"
	);
	return $info;
}

function allmodulegroup_install(){
	module_addhook("footer-modules");
	return true;
}

function allmodulegroup_uninstall(){
	return true;
}

function allmodulegroup_dohook($hookname,$args){
	global $session;
	tlschema("allmodulegroup");
	switch($hookname){
		case 'footer-modules':
			if($session['user']['superuser'] & SU_EDIT_COMMENTS) {
				tlschema("modulemanage");
				addnav("Module Categories");
				$sql = "SELECT count(*) as Anzahl FROM " . db_prefix("modules");
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				$count=$row['Anzahl'];
				addnav(array("Installierte Module (%s)",$count),"runmodule.php?module=allmodulegroup&op=active");
			}
			break;
	}
	return $args;
}

function allmodulegroup_run() {
	global $session;
	tlschema("modulemanage");
	page_header("Module Manager");
	$op = httpget('op');

	superusernav();
	//Module-Liste neu aufbauen
	addnav("Module Categories");
	addnav("",$REQUEST_URI);
	$module = httpget('module');

	if (is_array($module)){
		$modules = $module;
	}else{
		if ($module) $modules = array($module);
		else $modules = array();
	}

	reset($modules);

	$install_status = get_module_install_status();
	$uninstmodules = $install_status['uninstalledmodules'];
	$seencats = $install_status['installedcategories'];
	$ucount = $install_status['uninstcount'];

	ksort($seencats);
	addnav(array(" ?Uninstalled - (%s modules)", $ucount), "modules.php");
	reset($seencats);
	foreach ($seencats as $cat=>$count) {
		addnav(array(" ?%s - (%s modules)", $cat, $count), "modules.php?cat=$cat");
	}
	$sql = "SELECT count(*) as Anzahl FROM " . db_prefix("modules");
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$count=$row['Anzahl'];
	addnav(array("Installierte Module (%s)",$count),"runmodule.php?module=allmodulegroup&op=active");


	if ($op=="active") {
	 	//Listen
		output("`$`cAlle Modules`n`c`0");
		$activate = translate_inline("Activate");
		$uninstall = translate_inline("Uninstall");
		$reinstall = translate_inline("Reinstall");
		$strsettings = translate_inline("Settings");
		$strnosettings = translate_inline("`\$No Settings`0");
		$status = translate_inline("Status");
		$mname = translate_inline("Module Name");
		$ops = translate_inline("Ops");
		$mauth = translate_inline("Module Author");
		$inon = translate_inline("Installed On");
		$installstr = translate_inline("by %s");
		$active = translate_inline("`@Active`0");
		$inactive = translate_inline("`\$Inactive`0");
		rawoutput("<form action='modules.php?op=mass' method='POST'>");
		addnav("","modules.php?op=mass");
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' bgcolor='#999999'>",true);
		rawoutput(" <tr class='trhead'>");
		rawoutput("  <td>&nbsp;</td>");
		rawoutput("  <td>$ops</td>");
		rawoutput("  <td>$status</td>");
		rawoutput("  <td>$mname</td>");
		rawoutput("  <td>$mauth</td>");
		rawoutput("  <td>$inon</td>");
		rawoutput(" </tr>");

		$sql = "SELECT * FROM " . db_prefix("modules") . " ORDER BY active ASC, category ASC";
		$result = db_query($sql);
		if (db_num_rows($result)==0){
			rawoutput(" <tr class='trlight'>");
			rawoutput("  <td colspan='6' align='center'>");
			output("`i-- No Modules Installed--`i");
			rawoutput("  </td>");
			rawoutput(" </tr>");
		}
		
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			rawoutput(" <tr class='".($i%2?"trlight":"trdark")."'>",true);
			rawoutput("  <td nowrap valign='top'>");
			rawoutput("   <input type='checkbox' name='module[]' value=\"{$row['modulename']}\">");
			rawoutput("  </td>");
			rawoutput("  <td valign='top' nowrap>[ ");
			rawoutput("   <a href='modules.php?op=activate&module={$row['modulename']}'>");
			output_notl($activate);
			rawoutput("</a>");
			addnav("","modules.php?op=activate&module={$row['modulename']}");
		
			rawoutput(" |<a href='modules.php?op=uninstall&module={$row['modulename']}' onClick='return confirm(\"$uninstallconfirm\");'>");
			output_notl($uninstall);
			rawoutput("</a>");
			addnav("","modules.php?op=uninstall&module={$row['modulename']}");
			rawoutput(" | <a href='modules.php?op=reinstall&module={$row['modulename']}'>");
			output_notl($reinstall);
			rawoutput("</a>");
			addnav("","modules.php?op=reinstall&module={$row['modulename']}");

			if ($session['user']['superuser'] & SU_EDIT_CONFIG) {
				if (strstr($row['infokeys'], "|settings|")) {
					rawoutput(" | <a href='configuration.php?op=modulesettings&module={$row['modulename']}'>");
					output_notl($strsettings);
					rawoutput("</a>");
					addnav("","configuration.php?op=modulesettings&module={$row['modulename']}");
				} else {
					output_notl(" | %s", $strnosettings);
				}
			}
			
			rawoutput(" ]</td><td valign='top'>");
			output_notl($row['active']?$active:$inactive);			
			require_once("lib/sanitize.php");
			rawoutput("</td><td nowrap valign='top'><span title=\"".
					(isset($row['description'])&&$row['description']?
					 $row['description']:sanitize($row['formalname']))."\">");
			output_notl("%s", $row['formalname']);
			rawoutput("<br>");
			output_notl("(%s)", $row['modulename']);
			rawoutput("</span></td><td valign='top'>");
			output_notl("`#%s`0", $row['moduleauthor'], true);
			rawoutput("</td><td nowrap valign='top'>");
			$line = sprintf($installstr, $row['installedby']);
			output_notl("%s", $row['installdate']);
			rawoutput("<br>");
			output_notl("%s", $line);
			rawoutput("</td></tr>");
		}
		rawoutput("</table><br />");
		$activate = translate_inline("Activate");
		$deactivate = translate_inline("Deactivate");
		$reinstall = translate_inline("Reinstall");
		$uninstall = translate_inline("Uninstall");
		rawoutput("<input type='submit' name='activate' class='button' value='$activate'>");
		rawoutput("<input type='submit' name='deactivate' class='button' value='$deactivate'>");
		rawoutput("<input type='submit' name='reinstall' class='button' value='$reinstall'>");
		rawoutput("<input type='submit' name='uninstall' class='button' value='$uninstall'>");
		rawoutput("</form>");
	}
	
	page_footer();
}
?>