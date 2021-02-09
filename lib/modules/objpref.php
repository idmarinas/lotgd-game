<?php

/**
 * Delete all objtype for objid.
 *
 * @param string $objtype
 * @param int    $objid
 */
function module_delete_objprefs($objtype, $objid)
{
    global $mostrecentmodule;

    $repository = \Doctrine::getRepository('LotgdCore:ModuleObjprefs');
    $entities   = $repository->findBy(['objtype' => $objtype, 'objid' => $objid]);

    foreach ($entities as $entity)
    {
        \Doctrine::remove($entity);
    }

    \Doctrine::flush();

    \LotgdKernel::get('core.lotgd.cache')->invalidateTags(["module-objpref-{$objtype}-{$objid}"]);
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

    if (isset($info["prefs-{$objtype}"][$name]))
    {
        if (\is_array($info["prefs-{$objtype}"][$name]))
        {
            $v = $info["prefs-{$objtype}"][$name][0];
            $x = \explode('|', $v);
        }
        else
        {
            $x = \explode('|', $info["prefs-{$objtype}"][$name]);
        }

        if (isset($x[1]))
        {
            set_module_objpref($objtype, $objid, $name, $x[1], $module);

            return $x[1];
        }
    }
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

    $repository = \Doctrine::getRepository('LotgdCore:ModuleObjprefs');
    $entity     = $repository->findOneBy(['modulename' => $module, 'setting' => $name, 'objtype' => $objtype, 'objid' => $objid]);
    $entity     = $repository->hydrateEntity([
        'modulename' => $module,
        'setting'    => $name,
        'objtype'    => $objtype,
        'objid'      => $objid,
        'value'      => $value,
    ], $entity);

    \Doctrine::persist($entity);
    \Doctrine::flush();

    \LotgdKernel::get('core.lotgd.cache')->delete("module-objpref-{$objtype}-{$objid}-{$module}");
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

    $repository = \Doctrine::getRepository('LotgdCore:ModuleObjprefs');
    $entity     = $repository->findOneBy(['modulename' => $module, 'setting' => $name, 'objtype' => $objtype, 'objid' => $objid]);
    $entity     = $repository->hydrateEntity([
        'modulename' => $module,
        'setting'    => $name,
        'objtype'    => $objtype,
        'objid'      => $objid,
    ], $entity);

    $value = ((float) $entity->getValue()) + $value;
    $entity->setValue($value);

    \Doctrine::persist($entity);
    \Doctrine::flush();

    \LotgdKernel::get('core.lotgd.cache')->delete("module-objpref-{$objtype}-{$objid}-{$module}");
}

/**
 * Load objpref of a module.
 *
 * @param string $objtype
 * @param int    $objid
 * @param string $module
 */
function load_module_objpref($objtype, $objid, $module = false): array
{
    global $mostrecentmodule;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    $objid = (int) $objid;
    $cache = \LotgdKernel::get('core.lotgd.cache');

    return $cache->get("module-objpref-{$objtype}-{$objid}-{$module}", function ($item) use ($module, $objtype, $objid)
    {
        $item->expiresAfter(600);
        $item->tag("module-objpref-{$objtype}-{$objid}");

        $repository = \Doctrine::getRepository('LotgdCore:ModuleObjprefs');
        $result     = $repository->findBy(['modulename' => $module, 'objtype' => $objtype, 'objid' => $objid]);

        $module_objpref = [];

        foreach ($result as $val)
        {
            $module_objpref[$val->getSetting()] = $val->getValue();
        }

        return $module_objpref;
    });
}
