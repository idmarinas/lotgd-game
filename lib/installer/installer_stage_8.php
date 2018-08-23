<?php

require_once 'lib/installer/installer_functions.php';
require_once 'lib/sanitize.php';

$session['skipmodules'] = false;

if (array_key_exists('modulesok', $_POST))
{
    $session['moduleoperations'] = $_POST['modules'];
    $session['stagecompleted'] = $stage;
    header('Location: installer.php?stage='.($stage + 1));

    exit();
}
elseif (array_key_exists('moduleoperations', $session) && is_array($session['moduleoperations']))
{
    $session['stagecompleted'] = $stage;
}
else
{
    $session['stagecompleted'] = $stage - 1;
}
output('`@`c`bManage Modules`b`c');
output('Legend of the Green Dragon supports an extensive module system.');
output('Modules are small self-contained files that perform a specific function or event within the game.');
output('For the most part, modules are independant of each other, meaning that one module can be installed, uninstalled, activated, and deactivated without negative impact on the rest of the game.');
output("Not all modules are ideal for all sites, for example, there's a module called 'Multiple Cities,' which is intended only for large sites with many users online at the same time.");
output('`n`n`^If you are not familiar with Legend of the Green Dragon, and how the game is played, it is probably wisest to choose the default set of modules to be installed.');
output("`n`n`@There is an extensive community of users who write modules for LoGD at <a href='http://dragonprime.net/'>http://dragonprime.net/</a>.<br>", true);
$phpram = ini_get('memory_limit');

if (return_bytes($phpram) < 12582912 && -1 != $phpram && ! $session['overridememorylimit'] && ! $session['dbinfo']['upgrade'])
{ // 12 MBytes
    // enter this ONLY if it's not an upgrade and if the limit is really too low
    output('`n`n`$Warning: Your PHP memory limit is set to a very low level.');
    output('Smaller servers should not be affected by this during normal gameplay but for this installation step you should assign at least 12 Megabytes of RAM for your PHP process.');
    output('For now we will skip this step, but before installing any module, make sure to increase you memory limit.');
    output('`nYou can proceed at your own risk. Be aware that a blank screen indicates you *must* increase the memory limit.');
    output('`n`nTo override click again on "Set Up Modules".');
    $session['stagecompleted'] = 8;
    $session['overridememorylimit'] = true;
    $session['skipmodules'] = true;
}
else
{
    if (isset($session['overridememorylimit']) && $session['overridememorylimit'])
    {
        output('`4`n`nYou have been warned... you are now working on your own risk.`n`n');
        $session['skipmodules'] = false;
    }

    $submit = translate_inline('Save Module Settings');
    $install = translate_inline('Select Recommended Modules');
    $reset = translate_inline('Reset Values');
    $all_modules = [];
    $result = DB::query('SELECT * FROM '.DB::prefix('modules').' ORDER BY category, active DESC, formalname');

    if (false !== $result)
    {
        foreach ($result as $row)
        {
            if (! array_key_exists($row['category'], $all_modules))
            {
                $all_modules[$row['category']] = [];
            }
            $row['installed'] = true;
            $all_modules[$row['category']][$row['modulename']] = $row;
        }
    }
    $install_status = get_module_install_status();
    $uninstalled = $install_status['uninstalledmodules'];
    reset($uninstalled);
    $invalidmodule = [
        'version' => '',
        'author' => '',
        'category' => 'Invalid Modules',
        'download' => '',
        'description' => '',
        'invalid' => true,
    ];

    while (list($key, $modulename) = each($uninstalled))
    {
        $row = [];
        //test if the file is a valid module or a lib file/whatever that got in, maybe even malcode that does not have module form
        $file = file_get_contents("modules/$modulename.php");

        if (false === strpos($file, $modulename.'_getmoduleinfo') ||
            //strpos($file,$shortname."_dohook")===false ||
            //do_hook is not a necessity
            false === strpos($file, $modulename.'_install') ||
            false === strpos($file, $modulename.'_uninstall'))
        {
            //here the files has neither do_hook nor getinfo, which means it won't execute as a module here --> block it + notify the admin who is the manage modules section
            $modulenametext = $modulename.'.php '.appoencode(translate_inline('(`$Invalid Module! Contact Author or check file!`0)'));
            $moduleinfo = array_merge($invalidmodule, ['name' => $modulenametext]);
        }
        else
        {
            $moduleinfo = get_module_info($modulename);
        }
        //end of testing
        $row['installed'] = false;
        $row['active'] = false;
        $row['category'] = $moduleinfo['category'];
        $row['modulename'] = $modulename;
        $row['formalname'] = $moduleinfo['name'];
        $row['description'] = $moduleinfo['description'];
        $row['moduleauthor'] = $moduleinfo['author'];
        $row['invalid'] = (isset($moduleinfo['invalid'])) ? $moduleinfo['invalid'] : false;

        if (! array_key_exists($row['category'], $all_modules))
        {
            $all_modules[$row['category']] = [];
        }
        $all_modules[$row['category']][$row['modulename']] = $row;
    }
    output_notl('`0');
    rawoutput("<form action='installer.php?stage=".$stage."' method='POST'>");
    rawoutput("<input type='submit' name='modulesok' value='$submit' class='ui button'>");
    rawoutput("<input type='button' onClick='Lotgd.recommendedModules();' class='ui secondary button' value='$install'>");
    rawoutput("<input type='reset' value='$reset' class='ui yellow button'>");
    rawoutput("<table class='ui compact selectable striped table'>");
    ksort($all_modules);
    reset($all_modules);

    if (0 == count($all_modules))
    {
        output_notl('<tr class="center aligned"><td>`$Not modules found in folder "`b/modules`b"`0</td></tr>', true);
    }

    while (list($categoryName, $categoryItems) = each($all_modules))
    {
        rawoutput("<thead><tr class='center aligned'><th colspan='6'>".tl($categoryName).'</th></tr>');
        rawoutput('<tr><th>'.tl('Install module?').'</th><th>'.tl('Recommended').'</th><th>'.tl('Module Name').'</th><th>'.tl('Author').'</th></tr></thead><tbody>');
        reset($categoryItems);

        while (list($modulename, $moduleinfo) = each($categoryItems))
        {
            //if we specified things in a previous hit on this page, let's update the modules array here as we go along.
            $moduleinfo['realactive'] = $moduleinfo['active'];
            $moduleinfo['realinstalled'] = $moduleinfo['installed'];

            if (array_key_exists('moduleoperations', $session) && is_array($session['moduleoperations']) && array_key_exists($modulename, $session['moduleoperations']))
            {
                $ops = explode(',', $session['moduleoperations'][$modulename]);
                reset($ops);

                while (list($trash, $op) = each($ops))
                {
                    switch ($op)
                    {
                        case 'uninstall':
                            $moduleinfo['installed'] = false;
                            $moduleinfo['active'] = false;
                        break;
                        case 'install':
                            $moduleinfo['installed'] = true;
                            $moduleinfo['active'] = false;
                        break;
                        case 'activate':
                            $moduleinfo['installed'] = true;
                            $moduleinfo['active'] = true;
                        break;
                        case 'deactivate':
                            $moduleinfo['installed'] = true;
                            $moduleinfo['active'] = false;
                        break;
                        case 'donothing':
                        break;
                    }
                }
            }
            rawoutput('<tr>');

            if ($moduleinfo['realactive'])
            {
                $uninstallop = 'uninstall';
                $installop = 'deactivate';
                $activateop = 'donothing';
            }
            elseif ($moduleinfo['realinstalled'])
            {
                $uninstallop = 'uninstall';
                $installop = 'donothing';
                $activateop = 'activate';
            }
            else
            {
                $uninstallop = 'donothing';
                $installop = 'install';
                $activateop = 'install,activate';
            }
            $uninstallcheck = false;
            $installcheck = false;
            $activatecheck = false;

            if ($moduleinfo['active'])
            {
                $activatecheck = true;
            }
            elseif ($moduleinfo['installed'])
            {
                //echo "<font color='red'>$modulename is installed but not active.</font><br>";
                $installcheck = true;
            }
            else
            {
                //echo "$modulename is uninstalled.<br>";
                $uninstallcheck = true;
            }

            $isRecommended = in_array($modulename, $recommended_modules);
            $disabled = '';

            if (isset($moduleinfo['invalid']) && true == $moduleinfo['invalid'])
            {
                $disabled = 'disabled';
            }
            rawoutput('<td class="collapsing"><div class="ui radio checkbox">');
            rawoutput("<input type='radio' name='modules[$modulename]' id='uninstall-$modulename' value='$uninstallop'".($uninstallcheck ? ' checked' : '')." $disabled><label>".tl('Uninstalled').'</label>');
            rawoutput("</div>&nbsp;&nbsp;<div class='ui radio checkbox'><input type='radio' name='modules[$modulename]' id='install-$modulename' value='$installop'".($installcheck ? ' checked' : '')." $disabled><label>".tl('Installed').'</label>');
            rawoutput("</div>&nbsp;&nbsp;<div class='ui radio checkbox'><input type='radio' ".('' == $disabled && $isRecommended ? 'data-recommended' : null)." name='modules[$modulename]' id='activate-$modulename' value='$activateop' ".($activatecheck ? ' checked' : '')." $disabled><label>".tl('Activated').'</label>');
            rawoutput('</div></td>');

            output_notl('<td>'.($isRecommended ? tl('`^Yes`0') : tl('`$No`0')).'</td>', true);

            rawoutput('<td><span title="'.
            (isset($moduleinfo['description']) &&
            $moduleinfo['description'] ?
            $moduleinfo['description'] :
            sanitize($moduleinfo['formalname'])).'">');
            output_notl('`@');

            if (isset($moduleinfo['invalid']) && true == $moduleinfo['invalid'])
            {
                rawoutput($moduleinfo['formalname']);
            }
            else
            {
                output($moduleinfo['formalname']);
            }
            output_notl(" [`%$modulename`@]`0");
            rawoutput('</span></td><td>');
            output_notl("`#{$moduleinfo['moduleauthor']}`0", true);
            rawoutput('</td>');
            rawoutput('</tr>');
        }
    }
    rawoutput('</tbody></table>');
    rawoutput("<input type='submit' name='modulesok' value='$submit' class='ui button'>");
    rawoutput("<input type='button' onClick='Lotgd.recommendedModules();' class='ui secondary button' value='$install'>");
    rawoutput("<input type='reset' value='$reset' class='ui yellow button'>");
    rawoutput('</form>');

    if (! $session['dbinfo']['upgrade'])
    {
        rawoutput("<script language='JavaScript'>");
        rawoutput('Lotgd.recommendedModules();');
        rawoutput('</script>');
    }
}
