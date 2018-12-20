<?php

function activate_module($module)
{
    if (! is_module_installed($module))
    {
        if (! install_module($module))
        {
            return false;
        }
    }

    $update = DB::update('modules');
    $update->set(['active' => 1])
        ->where->equalTo('modulename', $module)
    ;
    DB::execute($update);
    invalidatedatacache("injections-inject-$module");
    massinvalidate('moduleprepare');

    if (DB::affected_rows() <= 0)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function deactivate_module($module)
{
    if (! is_module_installed($module))
    {
        if (! install_module($module))
        {
            return false;
        }
        else
        {
            //modules that weren't installed go to deactivated state by default in install_module
            return true;
        }
    }
    $update = DB::update('modules');
    $update->set(['active' => 0])
        ->where->equalTo('modulename', $module)
    ;
    DB::execute($update);
    invalidatedatacache("injections-inject-$module");
    massinvalidate('moduleprepare');

    if (DB::affected_rows() <= 0)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function uninstall_module($module)
{
    if (injectmodule($module, true))
    {
        $fname = $module.'_uninstall';
        debug('Running module uninstall script`n');
        tlschema("module-{$module}");

        if (! $fname())
        {
            return false;
        }
        tlschema();

        debug('Deleting module entry`n');
        $delete = DB::delete('modules');
        $delete->where->equalTo('modulename', $module);
        DB::execute($delete);

        debug('Deleting module hooks`n');
        module_wipehooks();

        debug('Deleting module settings`n');
        $delete = DB::delete('module_settings');
        $delete->where->equalTo('modulename', $module);
        DB::execute($delete);
        invalidatedatacache("modulesettings-settings-$module");

        debug('Deleting module user prefs`n');
        $delete = DB::delete('module_userprefs');
        $delete->where->equalTo('modulename', $module);
        DB::execute($delete);

        debug('Deleting module object prefs`n');
        $delete = DB::delete('module_objprefs');
        $delete->where->equalTo('modulename', $module);
        DB::execute($delete);
        invalidatedatacache("injections-inject-$module");
        massinvalidate('moduleprepare');

        return true;
    }
    else
    {
        return false;
    }
}

function install_module($module, $force = true)
{
    global $mostrecentmodule, $session;
    $name = $session['user']['name'];

    if (! $name)
    {
        $name = '`@System`0';
    }

    require_once 'lib/sanitize.php';

    if (modulename_sanitize($module) != $module)
    {
        debug("Error, module file names can only contain alpha numeric characters and underscores before the trailing .php`n`nGood module names include 'testmodule.php', 'joesmodule2.php', while bad module names include, 'test.module.php' or 'joes module.php'`n");

        return false;
    }
    else
    {
        // If we are forcing an install, then whack the old version.
        if ($force)
        {
            $delete = DB::delete('modules');
            $delete->where->equalTo('modulename', $module);
            DB::execute($delete);
        }
        // We want to do the inject so that it auto-upgrades any installed
        // version correctly.
        if (injectmodule($module, true))
        {
            // If we're not forcing and this is already installed, we are done
            if (! $force && is_module_installed($module))
            {
                return true;
            }

            $info = get_module_info($module);
            //check installation requirements
            if (! module_check_requirements($info['requires']))
            {
                debug('`$Module could not installed -- it did not meet its prerequisites.`n');

                return false;
            }
            else
            {
                $insert = DB::insert('modules');
                $insert->values([
                    'modulename' => $mostrecentmodule,
                    'formalname' => $info['name'],
                    'moduleauthor' => $info['author'],
                    'active' => 0,
                    'filename' => "{$mostrecentmodule}.php",
                    'installdate' => date('Y-m-d H:i:s'),
                    'installedby' => $name,
                    'category' => $info['category'],
                    'infokeys' => sprintf('|%s|', implode(array_keys($info), '|')),
                    'version' => $info['category'],
                    'download' => $info['download'],
                    'description' => $info['description']
                ]);
                DB::execute($insert);
                $fname = $mostrecentmodule.'_install';

                if (isset($info['settings']) && count($info['settings']) > 0)
                {
                    foreach ($info['settings'] as $key => $val)
                    {
                        if (is_array($val))
                        {
                            $x = explode('|', $val[0]);
                        }
                        else
                        {
                            $x = explode('|', $val);
                        }

                        if (isset($x[1]))
                        {
                            set_module_setting($key, $x[1]);
                            debug("Setting $key to default {$x[1]}");
                        }
                    }
                }

                if (false === $fname())
                {
                    return false;
                }

                debug('`^Module installed.  It is not yet active.`n');
                invalidatedatacache("injections-inject-$mostrecentmodule");
                massinvalidate('moduleprepare');

                return true;
            }
        }
        else
        {
            debug('`$Module could not be injected.');
            debug('Module not installed.');
            debug('This is probably due to the module file having a parse error or not existing in the filesystem.`n');

            return false;
        }
    }
}
