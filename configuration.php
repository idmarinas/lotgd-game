<?php

// translator ready
// addnews ready
// mail ready

require_once 'common.php';
require_once 'lib/showform.php';
include_once 'lib/gamelog.php';
require_once 'lib/superusernav.php';
//get arrays
require 'lib/data/configuration.php';
require 'lib/data/configuration_extended.php';

//-- Settings extended
$settings_extended = LotgdLocator::get(Lotgd\Core\Lib\SettingsExtended::class);

check_su_access(SU_EDIT_CONFIG);

tlschema('configuration');

$op = httpget('op');
$module = httpget('module');
$type_setting = httpget('settings');

//standardsettings
switch ($type_setting)
{
    case 'extended':
        switch ($op)
        {
            case 'save':
                $post = httpallpost();
                $old = $settings_extended->getArray();
                $current = $settings_extended->getArray();

                foreach ($post as $key => $val)
                {
                    $val = stripslashes($val);

                    if (! isset($current[$key]) || ($val != $current[$key]))
                    {
                        if (! isset($old[$key]))
                        {
                            $old[$key] = '';
                        }
                        $settings_extended->saveSetting($key, $val);

                        output('Setting %s to %s`n', $key, $val);

                        gamelog("`@Changed core setting (extended)`^$key`@ from `#".substr($old[$key], 25).'...`@ to `&'.substr($val, 25).'...`0', 'settings');
                        // Notify every module
                        modulehook('changesetting', ['module' => 'core', 'setting' => $key, 'old' => $old[$key], 'new' => $val], true);
                    }
                }
                output('`^Extended Settings saved.`0');
                $op = '';
                httpset($op, '');
            break;
        }
    break;
    default:
        switch ($op)
        {
            case 'save':
                if (1 == (int) httppost('blockdupemail') && 1 != (int) httppost('requirevalidemail'))
                {
                    httppostset('requirevalidemail', '1');
                    output('`brequirevalidemail has been set since blockdupemail was set.´b`n');
                }

                if (1 == (int) httppost('requirevalidemail') && 1 != (int) httppost('requireemail'))
                {
                    httppostset('requireemail', '1');
                    output('`brequireemail has been set since requirevalidemail was set.´b`n');
                }
                $defsup = httppost('defaultsuperuser');

                if ('' != $defsup)
                {
                    $value = 0;

                    foreach ($defsup as $k => $v)
                    {
                        if ($v)
                        {
                            $value += (int) $k;
                        }
                    }
                    httppostset('defaultsuperuser', $value);
                }
                $tmp = stripslashes(httppost('villagename'));

                if ($tmp && $tmp != getsetting('villagename', LOCATION_FIELDS))
                {
                    debug('Updating village name -- moving players');
                    $sql = 'UPDATE '.DB::prefix('accounts')." SET location='".
                        httppost('villagename')."' WHERE location='".
                        addslashes(getsetting('villagename', LOCATION_FIELDS))."'";
                    DB::query($sql);

                    if ($session['user']['location'] == getsetting('villagename', LOCATION_FIELDS))
                    {
                        $session['user']['location'] = stripslashes(httppost('villagename'));
                    }
                }
                $tmp = stripslashes(httppost('innname'));

                if ($tmp && $tmp != getsetting('innname', LOCATION_INN))
                {
                    debug('Updating inn name -- moving players');
                    $sql = 'UPDATE '.DB::prefix('accounts')." SET location='".
                        httppost('innname')."' WHERE location='".
                        addslashes(getsetting('innname', LOCATION_INN))."'";
                    DB::query($sql);

                    if ($session['user']['location'] == getsetting('innname', LOCATION_INN))
                    {
                        $session['user']['location'] = stripslashes(httppost('innname'));
                    }
                }

                if (stripslashes(httppost('motditems')) != getsetting('motditems', 5))
                {
                    invalidatedatacache('motd');
                }

                if (stripslashes(httppost('exp-array')) != getsetting('exp-array', '100,400,1002,1912,3140,4707,6641,8985,11795,15143,19121,23840,29437,36071,43930'))
                {
                    massinvalidate('exp_array_dk');
                }
                $post = httpallpost();

                $old = $settings->getArray();
                $current = $settings->getArray();

                foreach ($post as $key => $val)
                {
                    $val = stripslashes($val);

                    if (! isset($current[$key]) || ($val != $current[$key]))
                    {
                        if (! isset($old[$key]))
                        {
                            $old[$key] = '';
                        }
                        savesetting($key, $val);
                        output('Setting %s to %s`n', $key, $val);
                        gamelog("`@Changed core setting `^$key`@ from `#{$old[$key]}`@ to `&$val`0", 'settings');
                        // Notify every module
                        modulehook('changesetting', ['module' => 'core', 'setting' => $key, 'old' => $old[$key], 'new' => $val], true);
                    }
                }
                output('`^Settings saved.`0');
                $op = '';
                httpset($op, '');
            break;

            case 'modulesettings':
                if (injectmodule($module, true))
                {
                    $save = httpget('save');

                    if ('' != $save)
                    {
                        $module_settings = load_module_settings($module);
                        $old = $module_settings;
                        $post = httpallpost();
                        $post = modulehook('validatesettings', $post, true, $module);

                        if (isset($post['validation_error']))
                        {
                            $post['validation_error'] = translate_inline($post['validation_error']);
                            output('Unable to change settings:`$%s`0', $post['validation_error']);
                        }
                        else
                        {
                            reset($post);

                            foreach ($post as $key => $val)
                            {
                                $key = stripslashes($key);
                                $val = stripslashes($val);
                                set_module_setting($key, $val);

                                if (! isset($old[$key]) || $old[$key] != $val)
                                {
                                    output('Setting %s to %s`n', $key, $val);
                                    // Notify modules
                                    $oldval = '';

                                    if (isset($old[$key]))
                                    {
                                        $oldval = $old[$key];
                                    }
                                    gamelog("`@Changed module(`5$module`@) setting `^$key`@ from `#$oldval`@ to `&$val`0", 'settings');
                                    modulehook('changesetting', ['module' => $module, 'setting' => $key, 'old' => $oldval, 'new' => $val], true);
                                }
                            }
                            output('`^Module %s settings saved.`0`n', $module);
                        }
                        $save = '';
                        httpset('save', '');
                    }

                    if ('' == $save)
                    {
                        $info = get_module_info($module);

                        if (count($info['settings']) > 0)
                        {
                            $module_settings = load_module_settings($mostrecentmodule);
                            $msettings = [];

                            foreach ($info['settings'] as $key => $val)
                            {
                                if (is_array($val))
                                {
                                    $v = $val[0];
                                    $x = explode('|', $v);
                                    $val[0] = $x[0];
                                    $x[0] = $val;
                                }
                                else
                                {
                                    $x = explode('|', $val);
                                }
                                $msettings[$key] = $x[0];

                                if (! isset($module_settings[$key]) &&
                                        isset($x[1]))
                                {
                                    $module_settings[$key] = $x[1];
                                }
                            }
                            $msettings = modulehook('mod-dyn-settings', $msettings);

                            if (is_module_active($module))
                            {
                                output('This module is currently active: ');
                                $deactivate = translate_inline('Deactivate');
                                rawoutput("<a href='modules.php?op=deactivate&module={$module}&cat={$info['category']}'>");
                                output_notl($deactivate);
                                rawoutput('</a>');
                                addnav('', "modules.php?op=deactivate&module={$module}&cat={$info['category']}");
                            }
                            else
                            {
                                output('This module is currently deactivated: ');
                                $deactivate = translate_inline('Activate');
                                rawoutput("<a href='modules.php?op=activate&module={$module}&cat={$info['category']}'>");
                                output_notl($deactivate);
                                rawoutput('</a>');
                                addnav('', "modules.php?op=activate&module={$module}&cat={$info['category']}");
                            }
                            rawoutput("<form action='configuration.php?op=modulesettings&module=$module&save=1' method='POST'>", true);
                            addnav('', "configuration.php?op=modulesettings&module=$module&save=1");
                            tlschema("module-$module");
                            lotgd_showform($msettings, $module_settings);
                            tlschema();
                            rawoutput('</form>', true);
                        }
                        else
                        {
                            output('The %s module does not appear to define any module settings.', $module);
                        }
                    }
                }
                else
                {
                    output("I was not able to inject the module %s. Sorry it didn't work out.", htmlentities($module, ENT_COMPAT, getsetting('charset', 'UTF-8')));
                }
            break;
    }
}

page_header('Game Settings');

superusernav();
addnav('Module Manager', 'modules.php');

if ($module)
{
    $cat = $info['category'];
    addnav(['Module Category - `^%s`0', translate_inline($cat)], "modules.php?cat=$cat");
}

addnav('Game Settings');
addnav('Standard settings', 'configuration.php');
addnav('Extended settings', 'configuration.php?settings=extended');
addnav('Cache settings', 'configuration.php?settings=cache');
addnav('Cronjob settings', 'configuration.php?settings=cronjob');
addnav('', httpGetServer('REQUEST_URI'));

module_editor_navs('settings', 'configuration.php?op=modulesettings&module=');

switch ($type_setting)
{
    case 'extended':
        switch ($op)
        {
            case '':
                //this is just a way to check and insert a setting I deem necessary without going through the installer
                foreach ($setup_extended as $key => $val)
                {
                    $settings_extended->getSetting($key);
                }

                $vals = $settings_extended->getArray();

                rawoutput("<form action='configuration.php?settings=extended&op=save' method='POST'>");
                addnav('', 'configuration.php?settings=extended&op=save');
                lotgd_showform($setup_extended, $vals);
                rawoutput('</form>');
            break;
        }
    break;
    case 'cache':
        require_once 'lib/configuration/configuration_cache.php';
    break;
    case 'cronjob':
        require_once 'lib/configuration/configuration_cronjob.php';
    break;
    default:
        switch ($op)
        {
            case '':
                $enum = 'enumpretrans';
                require_once 'lib/datetime.php';
                $details = gametimedetails();
                $offset = getsetting('gameoffsetseconds', 0);

                for ($i = 0; $i <= 86400 / getsetting('daysperday', 4); $i += 300)
                {
                    $off = ($details['realsecstotomorrow'] - ($offset - $i));

                    if ($off < 0)
                    {
                        $off += 86400;
                    }
                    $x = strtotime('+'.$off.' secs');
                    $str = sprintf_translate('In %s at %s (+%s)',
                            reltime($x), date('h:i a', $x), date('H:i', $i));
                    $enum .= ",$i,$str";
                }
                rawoutput(tlbutton_clear());

                $secstonewday = secondstonextgameday($details);
                $useful_vals = [
                    'dayduration' => round(($details['dayduration'] / 60 / 60), 0).' hours',
                    'curgametime' => getgametime(),
                    'curservertime' => date('Y-m-d h:i:s a'),
                    'lastnewday' => date('h:i:s a',
                        strtotime("-{$details['realsecssofartoday']} seconds")),
                    'nextnewday' => date('h:i:s a',
                        strtotime("+{$details['realsecstotomorrow']} seconds")).' ('.date('H\\h i\\m s\\s', $secstonewday).')'
                ];

                //this is just a way to check and insert a setting I deem necessary without going through the installer
                if (getsetting('dpointspercurrencyunit', 100))
                {
                }

                $vals = $settings->getArray() + $useful_vals;

                rawoutput("<form action='configuration.php?op=save' method='POST'>");
                addnav('', 'configuration.php?op=save');
                lotgd_showform($setup, $vals);
                rawoutput('</form>');
            break;
        }
    break;
}

page_footer();
