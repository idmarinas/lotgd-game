<?php

/**
 * Delete all module user preferences.
 *
 * @param int $user
 */
function module_delete_userprefs(int $user)
{
    $delete = DB::delete('module_userprefs');
    $delete->where->equalTo('userid', $user);
    DB::execute($delete);

    massInvalidate("module-prefs-$user", true);
}

/**
 * Get all module prefs of a user.
 *
 * @param string $module
 * @param int    $user
 *
 * @return array
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
    if (! is_module_active($module))
    {
        return;
    }

    //we couldn't find this elsewhere, load the default value if it exists.
    $info = get_module_info($module);

    if (isset($info['prefs'][$name]))
    {
        if (is_array($info['prefs'][$name]))
        {
            $v = $info['prefs'][$name][0];
            $x = explode('|', $v);
        }
        else
        {
            $x = explode('|', $info['prefs'][$name]);
        }

        if (isset($x[1]))
        {
            set_module_pref($name, $x[1], $module, $user);

            return $x[1];
        }
    }

    return;
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
    if (! $session['user']['loggedin'] && ! $user)
    {
        // We do need to save to the loaded copy here however
        $module_prefs[$name] = $value;

        updatedatacache("module-prefs-$user-$module", $module_prefs, true);

        return;
    }

    if (isset($module_prefs[$name]))
    {
        $update = DB::update('module_userprefs');
        $update->set(['value' => $value])
            ->where->equalTo('modulename', $module)
                ->equalTo('setting', $name)
                ->equalTo('userid', $user)
        ;
        DB::execute($update);
    }
    else
    {
        $insert = DB::insert('module_userprefs');
        $insert->values([
            'modulename' => $module,
            'setting' => $name,
            'userid' => $user,
            'value' => $value
        ]);
        DB::execute($insert);
    }

    invalidatedatacache("module-prefs-$user-$module", true);
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

    $module_prefs = load_module_prefs($module, $uid);

    //don't write to the DB if the user isn't logged in.
    if (! $session['user']['loggedin'] && ! $user)
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

        updatedatacache("module-prefs-$user-$module", $module_prefs, true);

        return;
    }

    if (isset($module_prefs[$name]))
    {
        $update = DB::update('module_userprefs');
        $update->set(['value' => DB::expression("value+$value")])
            ->where->equalTo('modulename', $module)
                ->equalTo('setting', $name)
                ->equalTo('userid', $user)
        ;
        DB::execute($update);
    }
    else
    {
        $insert = DB::insert('module_userprefs');
        $insert->values([
            'modulename' => $module,
            'setting' => $name,
            'userid' => $user,
            'value' => $value
        ]);
        DB::execute($insert);
    }

    invalidatedatacache("module-prefs-$user-$module", true);
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
    if (! $session['user']['loggedin'] && ! $user)
    {
        // We do need to trash the loaded copy here however
        unset($module_prefs[$name]);

        updatedatacache("module-prefs-$user-$module", $module_prefs, true);

        return;
    }

    if (isset($module_prefs[$name]))
    {
        $delete = DB::delete('module_userprefs');
        $delete->where->equalTo('modulename', $module)
            ->equalTo('setting', $name)
            ->equalTo('userid', $user)
        ;
        DB::execute($delete);
    }

    invalidatedatacache("module-prefs-$user-$module", true);
}

/**
 * Load prefs of a module.
 *
 * @param string $module
 * @param int    $user
 *
 * @return array
 */
function load_module_prefs($module, $user = false): array
{
    global $session;

    if (false === $user)
    {
        $user = $session['user']['acctid'];
    }

    $module_prefs = datacache("module-prefs-$user-$module", 300, true);

    if (! is_array($module_prefs))
    {
        $module_prefs = [];
        $select = DB::select('module_userprefs');
        $select->columns(['setting', 'value'])
            ->where->equalTo('modulename', $module)
                ->equalTo('userid', $user)
        ;
        $result = DB::execute($select);

        while ($row = DB::fetch_assoc($result))
        {
            $module_prefs[$row['setting']] = $row['value'];
        }

        updatedatacache("module-prefs-$user-$module", $module_prefs, true);
    }

    return $module_prefs;
}
