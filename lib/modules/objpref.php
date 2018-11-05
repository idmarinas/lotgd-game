<?php

/**
 * Delete all objtype for objid.
 *
 * @param string $objtype
 * @param int    $objid
 */
function module_delete_objprefs($objtype, $objid)
{
    $delete = DB::delete('module_objprefs');
    $delete->where->equalTo('objtype', $objtype)
        ->equalTo('objid', $objid)
    ;
    DB::execute($delete);
    massinvalidate("module-objpref-$objtype-$objid-$module");
}

/**
 * Get value for a setting for a module, objtype and objid.
 *
 * @param string $objtype
 * @param int    $objid
 * @param string $name
 * @param string $module
 *
 * @return mixed
 */
function get_module_objpref($objtype, $objid, $name, $module = false)
{
    global $mostrecentmodule;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    $module_objpref = load_module_objpref($objtype, $objid, $module);

    if (isset($module_objpref[$name]))
    {
        return $module_objpref[$name];
    }

    //we couldn't find this elsewhere, load the default value if it exists.
    $info = get_module_info($module);

    if (isset($info["prefs-$objtype"][$name]))
    {
        if (is_array($info["prefs-$objtype"][$name]))
        {
            $v = $info["prefs-$objtype"][$name][0];
            $x = explode('|', $v);
        }
        else
        {
            $x = explode('|', $info["prefs-$objtype"][$name]);
        }

        if (isset($x[1]))
        {
            set_module_objpref($objtype, $objid, $name, $x[1], $module);

            return $x[1];
        }
    }

    return;
}

/**
 * Set value for a setting for a module, objtype and objid.
 *
 * @param string $objtype
 * @param int    $objid
 * @param string $name
 * @param mixed  $value
 * @param string $module
 */
function set_module_objpref($objtype, $objid, $name, $value, $module = false)
{
    global $mostrecentmodule;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    $module_objpref = load_module_objpref($objtype, $objid, $module);

    if (isset($module_objpref[$name]))
    {
        $update = DB::update('module_objprefs');
        $update->set(['value' => $value])
            ->where->equalTo('modulename', $module)
                ->equalTo('setting', $name)
                ->equalTo('objtype', $objtype)
                ->equalTo('objid', $objid)
        ;
        DB::execute($update);
    }
    else
    {
        $insert = DB::insert('module_objprefs');
        $insert->values([
            'value' => $value,
            'modulename' => $module,
            'setting' => $name,
            'objtype' => $objtype,
            'objid' => $objid
        ]);
        DB::execute($insert);
    }

    invalidatedatacache("module-objpref-$objtype-$objid-$module");
}

/**
 * Increment value for a setting for a module, objtype and objid.
 *
 * @param string    $objtype
 * @param int       $objid
 * @param string    $name
 * @param float|int $value
 * @param string    $module
 */
function increment_module_objpref($objtype, $objid, $name, $value = 1, $module = false)
{
    global $mostrecentmodule;

    $value = (float) $value;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    if (isset($module_objpref[$name]))
    {
        $update = DB::update('module_objprefs');
        $update->set(['value' => DB::expression("value+$value")])
            ->where->equalTo('modulename', $module)
                ->equalTo('setting', $name)
                ->equalTo('objtype', $objtype)
                ->equalTo('objid', $objid)
        ;
        DB::execute($update);
    }
    else
    {
        $insert = DB::insert('module_objprefs');
        $insert->values([
            'value' => $value,
            'modulename' => $module,
            'setting' => $name,
            'objtype' => $objtype,
            'objid' => $objid
        ]);
        DB::execute($insert);
    }

    invalidatedatacache("module-objpref-$objtype-$objid-$module");
}

/**
 * Load objpref of a module.
 *
 * @param string $objtype
 * @param int    $objid
 * @param string $module
 *
 * @return array
 */
function load_module_objpref($objtype, $objid, $module = false): array
{
    global $mostrecentmodule;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    $module_objpref = datacache("module-objpref-$objtype-$objid-$module", 900, true);

    if (! is_array($module_objpref))
    {
        $module_objpref = [];
        $select = DB::select('module_objprefs');
        $select->columns(['setting', 'value'])
            ->where->equalTo('modulename', $module)
                ->equalTo('objtype', $objtype)
                ->equalTo('objid', $objid)
        ;
        $result = DB::execute($select);

        while ($row = DB::fetch_assoc($result))
        {
            $module_objpref[$row['setting']] = $row['value'];
        }

        updatedatacache("module-objpref-$objtype-$objid-$module", $module_objpref, true);
    }

    return $module_objpref;
}
