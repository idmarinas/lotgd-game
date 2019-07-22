<?php

function activate_module($module)
{
    if (! is_module_installed($module) && ! install_module($module))
    {
        return false;
    }

    $repository = \Doctrine::getRepository('LotgdCore:Modules');
    $entity = $repository->find($module);

    $entity->setActive(1);

    \Doctrine::persist($entity);
    \Doctrine::flush();

    invalidatedatacache("injections-inject-$module");
    massinvalidate('moduleprepare');

    return true;
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

    $repository = \Doctrine::getRepository('LotgdCore:Modules');
    $entity = $repository->find($module);

    $entity->setActive(0);

    \Doctrine::persist($entity);
    \Doctrine::flush();

    invalidatedatacache("injections-inject-$module");
    massinvalidate('moduleprepare');

    return true;
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

        $repository = \Doctrine::getRepository('LotgdCore:Modules');
        $entity = $repository->find($module);

        debug('Deleting module entry`n');
        \Doctrine::remove($entity);

        debug('Deleting module hooks`n');
        module_wipehooks($module);

        debug('Deleting module settings`n');
        $repository = \Doctrine::getRepository('LotgdCore:ModuleSettings');
        $entities = $repository->findBy([ 'modulename' => $module ]);
        foreach ($entities as $entity)
        {
            \Doctrine::remove($entity);
        }
        invalidatedatacache("modulesettings-settings-$module");

        debug('Deleting module user prefs`n');
        $repository = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');
        $entities = $repository->findBy([ 'modulename' => $module ]);
        foreach ($entities as $entity)
        {
            \Doctrine::remove($entity);
        }

        debug('Deleting module object prefs`n');
        $repository = \Doctrine::getRepository('LotgdCore:ModuleObjprefs');
        $entities = $repository->findBy([ 'modulename' => $module ]);
        foreach ($entities as $entity)
        {
            \Doctrine::remove($entity);
        }
        invalidatedatacache("injections-inject-$module");
        massinvalidate('moduleprepare');

        \Doctrine::flush();

        return true;
    }

    return false;
}

function install_module($module, $force = true)
{
    global $mostrecentmodule, $session;

    $name = $session['user']['name'] ?: '`@System`0';

    if (LotgdSanitize::moduleNameSanitize($module) != $module)
    {
        debug("Error, module file names can only contain alpha numeric characters and underscores before the trailing .php`n`nGood module names include 'testmodule.php', 'joesmodule2.php', while bad module names include, 'test.module.php' or 'joes module.php'`n");

        return false;
    }
    else
    {
        $repository = \Doctrine::getRepository('LotgdCore:Modules');
        // If we are forcing an install, then whack the old version.
        if ($force)
        {
            $entity = $repository->find($module);
            if ($entity)
            {
                \Doctrine::remove($entity);
                \Doctrine::flush();
            }
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
                $entity = $repository->hydrateEntity([
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
                \Doctrine::persist($entity);
                \Doctrine::flush();

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
