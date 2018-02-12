<?php
// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/sanitize.php';
require_once 'lib/superusernav.php';

check_su_access(SU_MANAGE_MODULES);

tlschema('modulemanage');

page_header('Module Manager');

superusernav();

addnav('Module Categories');

addnav('', $REQUEST_URI);
$op = httpget('op');
$module = httpget('module');

if ($op == 'mass')
{
	if (httppost('activate')) $op = 'activate';
	if (httppost('deactivate')) $op = 'deactivate';
	if (httppost('uninstall')) $op = 'uninstall';
	if (httppost('reinstall')) $op = 'reinstall';
    if (httppost('install')) $op = 'install';

	$module = httppost('module');
}
$theOp = $op;
if (is_array($module))
{
	$modules = $module;
}
else
{
	if ($module) $modules = [$module];
	else $modules = [];
}
reset($modules);
foreach ($modules as $key => $module)
{
	$op = $theOp;
	output("`2Performing `^%s`2 on `%%s`0`n", translate_inline($op), $module);
    if($op == 'install')
    {
        if (install_module($module)) {}
        else
        {
			httpset('cat','');
			output("`\$Error, module could not be installed!`n`n");
		}
		$op="";
		httpset('op', "");
		massinvalidate("hook");
		massinvalidate("module-prepare");
    }
    elseif($op == 'uninstall')
    {
        if (uninstall_module($module)) { }
        else
        {
			output("Unable to inject module.  Module not uninstalled.`n");
		}
		$op="";
		httpset('op', "");
		massinvalidate("hook");
		massinvalidate("module-prepare");
		invalidatedatacache("inject-$module");
    }
    elseif($op == 'activate')
    {
		activate_module($module);
		$op = '';
		httpset('op', '');
		invalidatedatacache("inject-$module");
		massinvalidate('hook');
		massinvalidate('module-prepare');
		injectmodule($module, true);
    }
    elseif($op == "deactivate")
    {
		deactivate_module($module);
		$op = '';
		httpset('op', '');
		invalidatedatacache("inject-$module");
		massinvalidate('module-prepare');
    }
    elseif($op == 'reinstall')
    {
		$sql = "UPDATE " . DB::prefix("modules") . " SET filemoddate='0000-00-00 00:00:00' WHERE modulename='$module'";
		DB::query($sql);
		// We don't care about the return value here at all.
		$op="";
		httpset('op', "");
		invalidatedatacache("inject-$module");
		massinvalidate("hook");
		massinvalidate("module-prepare");
		injectmodule($module, true);
	}
}

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

$cat = httpget('cat');

if ($op == '')
{
    if ($cat)
    {
		$sortby = httpget('sortby');
		if (!$sortby) $sortby = 'installdate';
		$order = httpget('order');
		$tcat = translate_inline($cat);
		output('`n`b%s Modules`b`n', $tcat);
		$deactivate = translate_inline('Deactivate');
		$activate = translate_inline('Activate');
		$uninstall = translate_inline('Uninstall');
		$reinstall = translate_inline('Reinstall');
		$strsettings = translate_inline('Settings');
		$strnosettings = translate_inline('No Settings');
		$uninstallconfirm = translate_inline('Are you sure you wish to uninstall this module?  All user preferences and module settings will be lost.  If you wish to temporarily remove access to the module, you may simply deactivate it.');
		$status = translate_inline('Status');
		$mname = translate_inline('Module Name');
		$ops = translate_inline('Ops');
		$mauth = translate_inline('Module Author');
		$inon = translate_inline('Installed On');
		$installstr = translate_inline('by %s');
		$active = translate_inline('`@Active`0');
		$inactive = translate_inline('`$Inactive`0');
		rawoutput("<form action='modules.php?op=mass&cat=$cat' method='POST'>");
		addnav('', "modules.php?op=mass&cat=$cat");
		rawoutput("<table class='ui small very compact selectable striped table'>",true);
		rawoutput("<thead><tr><th>&nbsp;</th><th>$ops</th><th><a href='modules.php?cat=$cat&sortby=active&order=".($sortby=="active"?!$order:1)."'>$status</a></th><th><a href='modules.php?cat=$cat&sortby=formalname&order=".($sortby=="formalname"?!$order:1)."'>$mname</a></th><th><a href='modules.php?cat=$cat&sortby=moduleauthor&order=".($sortby=="moduleauthor"?!$order:1)."'>$mauth</a></th><th><a href='modules.php?cat=$cat&sortby=installdate&order=".($sortby == "installdate"?!$order:0)."'>$inon</a></th></thead></tr>");
		addnav('', "modules.php?cat=$cat&sortby=active&order=".($sortby=="active"?!$order:1));
		addnav('', "modules.php?cat=$cat&sortby=formalname&order=".($sortby=="formalname"?!$order:1));
		addnav('', "modules.php?cat=$cat&sortby=moduleauthor&order=".($sortby=="moduleauthor"?!$order:1));
		addnav('', "modules.php?cat=$cat&sortby=installdate&order=".($sortby=="installdate"?$order:0));
		$sql = "SELECT * FROM " . DB::prefix("modules") . " WHERE category='$cat' ORDER BY ".$sortby." ".($order?"ASC":"DESC");
		$result = DB::query($sql);
        if ($result->count() == 0)
        {
			rawoutput("<tr><td colspan='6' align='center'>");
			output("`i-- No Modules Installed--`i");
			rawoutput("</td></tr>");
        }

        for ($i = 0; $i < $result->count(); $i++)
        {
			$row = DB::fetch_assoc($result);
			rawoutput("<tr>",true);
			rawoutput("<td class='collapsing'>");
			rawoutput("<div class='ui checkbox'><input type='checkbox' name='module[]' value=\"{$row['modulename']}\"></div>");
			rawoutput("</td><td class='collapsing'>");
            if ($row['active'])
            {
				rawoutput("<a data-tooltip='$deactivate' href='modules.php?op=deactivate&module={$row['modulename']}&cat=$cat'>");
				output_notl('<i class="green link power icon"></i>', true);
				rawoutput("</a>");
				addnav("","modules.php?op=deactivate&module={$row['modulename']}&cat=$cat");
            }
            else
            {
				rawoutput("<a data-tooltip='$activate' href='modules.php?op=activate&module={$row['modulename']}&cat=$cat'>");
				output_notl('<i class="red link power icon"></i>', true);
				rawoutput("</a>");
				addnav("","modules.php?op=activate&module={$row['modulename']}&cat=$cat");
            }
            $options = json_encode(['html' => $uninstallconfirm]);
            rawoutput(" <a data-tooltip='$uninstall' href='modules.php?op=uninstall&module={$row['modulename']}&cat=$cat' data-options='$options' onclick='Lotgd.confirm(this, event)'>");

			output_notl('<i class="red corner remove icon"></i>', true);
			rawoutput("</a>");
			addnav('',"modules.php?op=uninstall&module={$row['modulename']}&cat=$cat");
			rawoutput(" <a data-tooltip='$reinstall' href='modules.php?op=reinstall&module={$row['modulename']}&cat=$cat'>");
			output_notl('<i class="orange corner undo icon"></i>', true);
			rawoutput("</a>");
			addnav('', "modules.php?op=reinstall&module={$row['modulename']}&cat=$cat");

            if ($session['user']['superuser'] & SU_EDIT_CONFIG)
            {
				if (strstr($row['infokeys'], "|settings|"))
				{
					rawoutput(" <a data-tooltip='$strsettings' href='configuration.php?op=modulesettings&module={$row['modulename']}'>");
					output_notl('<i class="blue link settings icon"></i>', true);
					rawoutput("</a>");
					addnav('', "configuration.php?op=modulesettings&module={$row['modulename']}");
				}
				else
				{
					output_notl(' <span data-tooltip="%s"><i class="red settings icon"></i></span>', $strnosettings, true);
				}
			}

			rawoutput('</td><td>');
            output_notl($row['active'] ? $active : $inactive);

			rawoutput("</td><td nowrap><span title='". (isset($row['description']) && $row['description'] ? $row['description'] : sanitize($row['formalname']))."'>");
			output_notl($row['formalname']);
			rawoutput("<br>");
			output_notl("(%s) V%s", $row['modulename'],$row['version']);
			rawoutput("</span></td><td>");
			output_notl("`#%s`0", $row['moduleauthor'], true);
			rawoutput("</td><td nowrap>");
			$line = sprintf($installstr, $row['installedby']);
			output_notl($row['installdate']);
			rawoutput("<br>");
			output_notl($line);
			rawoutput("</td></tr>");
		}
		rawoutput('</table><br>');
		$activate = translate_inline('Activate');
		$deactivate = translate_inline('Deactivate');
		$reinstall = translate_inline('Reinstall');
		$uninstall = translate_inline('Uninstall');
		rawoutput("<input type='submit' name='activate' class='ui button' value='$activate'>");
		rawoutput("<input type='submit' name='deactivate' class='ui secondary button' value='$deactivate'>");
		rawoutput("<input type='submit' name='reinstall' class='ui yellow button' value='$reinstall'>");
		rawoutput("<input type='submit' name='uninstall' class='ui negative button' value='$uninstall'>");
		rawoutput("</form>");
    }
    else
    {
		$sorting = httpget('sorting');
		if (!$sorting) $sorting="shortname";
		$order = httpget('order');
		output('`bUninstalled Modules`b`n');
		$install = translate_inline("Install");
		$notinstallable = translate_inline("Not installable");
		$mname = translate_inline("Module Name");
		$ops = translate_inline("Ops");
		$mauth = translate_inline("Module Author");
		$categ = translate_inline("Category");
		$fname = translate_inline("Filename");
		rawoutput("<form action='modules.php?op=mass&cat=$cat' method='POST'>");
		addnav('', "modules.php?op=mass&cat=$cat");
		rawoutput("<table class='ui small very compact selectable striped table'>",true);
		rawoutput("<thead><tr><th>&nbsp;</th><th>$ops</th><th><a href='modules.php?sorting=name&order=".($sorting=="name"?!$order:0)."'>$mname</a></th><th><a href='modules.php?sorting=author&order=".($sorting=="author"?!$order:0)."'>$mauth</a></th><th><a href='modules.php?sorting=category&order=".($sorting=="category"?!$order:0)."'>$categ</a></th><th><a href='modules.php?sorting=shortname&order=".($sorting=="shortname"?!$order:0)."'>$fname</a></th></tr></thead>");
		addnav('', "modules.php?sorting=name&order=".($sorting=="name"?!$order:0));
		addnav('', "modules.php?sorting=author&order=".($sorting=="author"?!$order:0));
		addnav('', "modules.php?sorting=category&order=".($sorting=="category"?!$order:0));
		addnav('', "modules.php?sorting=shortname&order=".($sorting=="shortname"?!$order:0));
        if (count($uninstmodules) > 0)
        {
			$count = 0;
			$moduleinfo = [];
			$sortby = [];
			$numberarray = [];
			$invalidmodule = [
				'version' => '',
				'author' => '',
				'category' => '',
				'download' => '',
				'invalid' => true,
            ];
            foreach($uninstmodules as $key => $shortname)
            {
				//test if the file is a valid module or a lib file/whatever that got in, maybe even malcode that does not have module form
				$shortname = strtolower($shortname);
				$file = file_get_contents("modules/$shortname.php");
				if (strpos($file, $shortname."_getmoduleinfo") === false ||
					//strpos($file,$shortname."_dohook")===false ||
					//do_hook is not a necessity
					strpos($file,$shortname."_install")===false ||
					strpos($file,$shortname."_uninstall")===false) {
					//here the files has neither do_hook nor getinfo, which means it won't execute as a module here --> block it + notify the admin who is the manage modules section
					$temp = array_merge($invalidmodule,array("name"=>$shortname.".php ".appoencode(translate_inline("(`\$Invalid Module! Contact Author or check file!`0)"))));
                }
                else
                {
					$temp= get_module_info($shortname);
				}
				//end of testing
				if (!$temp || empty($temp)) continue;
				$temp['shortname']=$shortname;
				array_push($moduleinfo,$temp);
				array_push($sortby, full_sanitize($temp[$sorting]));
				array_push($numberarray, $count);
				$count++;
			}
			array_multisort($sortby,($order?SORT_DESC:SORT_ASC),$numberarray,($order?SORT_DESC:SORT_ASC));
            for ($a = 0; $a < count($moduleinfo); $a++)
            {
				$i = $numberarray[$a];
				rawoutput("<tr>");
                $description = (isset($moduleinfo[$i]['description']) ? $moduleinfo[$i]['description'] : '');
				if (isset($moduleinfo[$i]['invalid']) && $moduleinfo[$i]['invalid']===true)
				{
					rawoutput("<td></td><td>");
					output_notl('<span data-tooltip="%s"><i class="red configure icon"></i></span>', $notinstallable, true);
					rawoutput("</td>");
                    rawoutput('<td colspan="3"><span title="'.($description?$description:sanitize($moduleinfo[$i]['name'])).'">');
                    rawoutput($moduleinfo[$i]['name']." ".$moduleinfo[$i]['version']);
				    rawoutput("</span>");
				}
				else
				{
					rawoutput("<td class='collapsing'><div class='ui checkbox'><input type='checkbox' name='module[]' value='{$moduleinfo[$i]['shortname']}'></div></td>");
					rawoutput("<td class='collapsing'>");
					rawoutput("<a href='modules.php?op=install&module={$moduleinfo[$i]['shortname']}&cat={$moduleinfo[$i]['category']}'>");
					output_notl('<span data-tooltip="%s"><i class="green link configure icon"></i></span>', $install, true);
					rawoutput("</a></td>");
					addnav("","modules.php?op=install&module={$moduleinfo[$i]['shortname']}&cat={$moduleinfo[$i]['category']}");
                    rawoutput('<td><span title="'.($description?$description:sanitize($moduleinfo[$i]['name'])).'">');
                    rawoutput($moduleinfo[$i]['name']." ".$moduleinfo[$i]['version']);
				    rawoutput("</span></td><td>");
				    output_notl("`#%s`0", $moduleinfo[$i]['author'], true);
                    rawoutput("</td><td>");
                    rawoutput($moduleinfo[$i]['category']);
				}
				rawoutput('</td><td>');
				rawoutput($moduleinfo[$i]['shortname'] . ".php");
				rawoutput('</td></tr>');
                if (isset($moduleinfo[$i]['requires']) && count($moduleinfo[$i]['requires']))
                {
					rawoutput('<tr><td>&nbsp;</td>');
					rawoutput("<td colspan='5'>");
					output("`bRequires:`b`n");
					reset($moduleinfo[$i]['requires']);
                    foreach ($moduleinfo[$i]['requires'] as $key=>$val)
                    {
						$info = explode("|",$val);
                        if (module_check_requirements(array($key=>$val)))
                        {
							output_notl("`@");
                        }
                        else
                        {
							output_notl("`\$");
						}
						if (isset($info[1])) output_notl("$key {$info[0]} -- {$info[1]}`n");
						else output_notl("$key {$info[0]}`n");
					}
					rawoutput('</td></tr>');
				}
				$count++;
			}
        }
        else
        {
			rawoutput("<tr><td colspan='6' class='center aligned'>");
			output('`i--No uninstalled modules were found--`i');
			rawoutput('</td></tr>');
		}
		rawoutput('</table><br>');
		$install = translate_inline('Install');
		rawoutput("<input type='submit' name='install' class='ui button' value='$install'>");
	}
}

page_footer();
