<?php

/**
 * Delete all module user preferences.
 */
function module_delete_userprefs(int $user)
{
    $repository = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');
    $entities   = $repository->findBy(['userid' => $user]);

    foreach ($entities as $entity)
    {
        \Doctrine::remove($entity);
    }

    \Doctrine::flush();
}

/**
 * Get all module prefs of a user.
 *
 * @param string $module
 * @param int    $user
 */
function get_all_module_prefs($module = false, $user = false): array
{
    global $mostrecentmodule, $session;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    if (false === $user)
    {
        $user = $session['user']['acctid'];
    }

    return load_module_prefs($module, $user);
}

/**
 * Get pref of user for a given module and setting.
 *
 * @param string $name
 * @param string $module
 * @param int    $user
 *
 * @return mixed
 */
function get_module_pref($name, $module = false, $user = false)
{
    global $module_prefs,$mostrecentmodule,$session;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    if (false === $user)
    {
        if (isset($session['user']['loggedin']) && $session['user']['loggedin'])
        {
            $user = $session['user']['acctid'];
        }
        else
        {
            $user = 0;
        }
    }

    $module_prefs = load_module_prefs($module, $user);

    if (isset($module_prefs[$name]))
    {
        return $module_prefs[$name];
    }

    //-- If module is not active, return null
    if ( ! is_module_active($module))
    {
        return;
    }

    //we couldn't find this elsewhere, load the default value if it exists.
    $info = get_module_info($module);

    if (isset($info['prefs'][$name]))
    {
        if (\is_array($info['prefs'][$name]))
        {
            $v = $info['prefs'][$name][0];
            $x = \explode('|', $v);
        }
        else
        {
            $x = \explode('|', $info['prefs'][$name]);
        }

        if (isset($x[1]))
        {
            set_module_pref($name, $x[1], $module, $user);

            return $x[1];
        }
    }
}

/**
 * Set pref of user for a given module and setting.
 *
 * @param string $name
 * @param mixed  $value
 * @param string $module
 * @param int    $user
 */
function set_module_pref($name, $value, $module = false, $user = false)
{
    global $mostrecentmodule, $session;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    if (false === $user)
    {
        $user = $session['user']['acctid'];
    }

    $module_prefs = load_module_prefs($module, $user);

    //don't write to the DB if the user isn't logged in.
    if ( ! ($session['user']['loggedin'] ?? false) && ! $user)
    {
        // We do need to save to the loaded copy here however
        $module_prefs[$name] = $value;

        return;
    }

    $repository = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');
    $entity     = $repository->findOneBy(['modulename' => $module, 'setting' => $name, 'userid' => $user]);
    $entity     = $repository->hydrateEntity([
        'modulename' => $module,
        'setting'    => $name,
        'userid'     => $user,
        'value'      => $value,
    ], $entity);

    \Doctrine::persist($entity);
    \Doctrine::flush();
}

/**
 * Increment pref of user for a given module and setting.
 *
 * @param string    $name
 * @param int|float $value
 * @param string    $module
 * @param int       $user
 */
function increment_module_pref($name, $value = 1, $module = false, $user = false)
{
    global $mostrecentmodule, $session;

    $value = (float) $value;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    if (false === $user)
    {
        $user = $session['user']['acctid'];
    }

    $module_prefs = load_module_prefs($module, $user);

    //don't write to the DB if the user isn't logged in.
    if ( ! ($session['user']['loggedin'] ?? false) && ! $user)
    {
        // We do need to save to the loaded copy here however
        if (isset($module_prefs[$name]))
        {
            $module_prefs[$name] += $value;
        }
        else
        {
            $module_prefs[$name] = $value;
        }

        return;
    }

    $repository = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');
    $entity     = $repository->findOneBy(['modulename' => $module, 'setting' => $name, 'userid' => $user]);

    if ( ! $entity)
    {
        $entity = new \Lotgd\Core\Entity\ModuleUserprefs();
        $entity->setModulename($module)
            ->setSetting($name)
            ->setUserid($user)
        ;
    }

    $entity->setValue((float) ($entity->getValue()) + $value);

    \Doctrine::persist($entity);
    \Doctrine::flush();
}

/**
 * Clear a setting for a given module and user.
 *
 * @param string $name
 * @param string $module
 * @param int    $user
 */
function clear_module_pref($name, $module = false, $user = false)
{
    global $mostrecentmodule, $session;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    if (false === $user)
    {
        $user = $session['user']['acctid'];
    }

    $module_prefs = load_module_prefs($module, $user);

    //don't write to the DB if the user isn't logged in.
    if ( ! ($session['user']['loggedin'] ?? false) && ! $user)
    {
        // We do need to trash the loaded copy here however
        unset($module_prefs[$name]);

        return;
    }

    $repository = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');
    $entity     = $repository->findOneBy(['modulename' => $module, 'setting' => $name, 'userid' => $user]);

    if ($entity)
    {
        \Doctrine::remove($entity);
        \Doctrine::flush();
    }
}

/**
 * Load prefs of a module.
 *
 * @param string $module
 * @param int    $user
 */
function load_module_prefs($module, $user = false): array
{
    global $session;

    if (false === $user)
    {
        $user = $session['user']['acctid'];
    }

    $repository = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');

    $result = $repository->findBy(['modulename' => $module, 'userid' => $user]);

    $module_prefs = [];

    foreach ($result as $val)
    {
        $module_prefs[$val->getSetting()] = $val->getValue();
    }

    return $module_prefs;
}
