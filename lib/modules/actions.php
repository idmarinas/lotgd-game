<?php

function activate_module($module)
{
    if ( ! is_module_installed($module) && ! install_module($module))
    {
        return false;
    }

    $repository = \Doctrine::getRepository('LotgdCore:Modules');
    $entity     = $repository->find($module);

    $entity->setActive(1);

    \Doctrine::persist($entity);
    \Doctrine::flush();

    return true;
}

function deactivate_module($module)
{
    if ( ! is_module_installed($module))
    {
        // modules that weren't installed go to deactivated state by default in install_module
        return ! install_module($module);
    }

    $repository = \Doctrine::getRepository('LotgdCore:Modules');
    $entity     = $repository->find($module);

    $entity->setActive(0);

    \Doctrine::persist($entity);
    \Doctrine::flush();

    return true;
}

function uninstall_module($module)
{
    if (injectmodule($module, true))
    {
        $fname = $module.'_uninstall';
        \LotgdResponse::pageDebug('Running module uninstall script`n');

        if ( ! $fname())
        {
            return false;
        }

        $repository = \Doctrine::getRepository('LotgdCore:Modules');
        $entity     = $repository->find($module);

        \LotgdResponse::pageDebug('Deleting module entry`n');
        \Doctrine::remove($entity);

        \LotgdResponse::pageDebug('Deleting module hooks`n');
        module_wipehooks($module);

        \LotgdResponse::pageDebug('Deleting module settings`n');
        $repository = \Doctrine::getRepository('LotgdCore:ModuleSettings');
        $entities   = $repository->findBy(['modulename' => $module]);

        foreach ($entities as $entity)
        {
            \Doctrine::remove($entity);
        }

        \LotgdResponse::pageDebug('Deleting module user prefs`n');
        $repository = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');
        $entities   = $repository->findBy(['modulename' => $module]);

        foreach ($entities as $entity)
        {
            \Doctrine::remove($entity);
        }

        \LotgdResponse::pageDebug('Deleting module object prefs`n');
        $repository = \Doctrine::getRepository('LotgdCore:ModuleObjprefs');
        $entities   = $repository->findBy(['modulename' => $module]);

        foreach ($entities as $entity)
        {
            \Doctrine::remove($entity);
        }

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
        \LotgdResponse::pageDebug("Error, module file names can only contain alpha numeric characters and underscores before the trailing .php`n`nGood module names include 'testmodule.php', 'joesmodule2.php', while bad module names include, 'test.module.php' or 'joes module.php'`n");

        return false;
    }

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
        if ( ! $force && is_module_installed($module))
        {
            return true;
        }

        $info = get_module_info($module);
        // check installation requirements
        if ( ! module_check_requirements($info['requires']))
        {
            \LotgdResponse::pageDebug('`$Module could not installed -- it did not meet its prerequisites.`n');

            return false;
        }

        $entity = $repository->hydrateEntity([
            'modulename'   => $mostrecentmodule,
            'formalname'   => $info['name'],
            'moduleauthor' => $info['author'],
            'active'       => 0,
            'filename'     => "{$mostrecentmodule}.php",
            'installdate'  => new \DateTime('now'),
            'installedby'  => $name,
            'category'     => $info['category'],
            'infokeys'     => sprintf('|%s|', implode('|', array_keys($info))),
            'version'      => $info['version'],
            'download'     => $info['download'],
            'description'  => $info['description'],
        ]);
        \Doctrine::persist($entity);
        \Doctrine::flush();

        $fname = $mostrecentmodule.'_install';

        if (isset($info['settings']) && \count($info['settings']) > 0)
        {
            foreach ($info['settings'] as $key => $val)
            {
                if (\is_array($val))
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
                    \LotgdResponse::pageDebug("Setting {$key} to default {$x[1]}");
                }
            }
        }

        if (false === $fname())
        {
            return false;
        }

        \LotgdResponse::pageDebug('`^Module installed.  It is not yet active.`n');

        return true;
    }

    \LotgdResponse::pageDebug('`$Module could not be injected.');
    \LotgdResponse::pageDebug('Module not installed.');
    \LotgdResponse::pageDebug('This is probably due to the module file having a parse error or not existing in the filesystem.`n');

    return false;
}
