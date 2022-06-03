<?php

/**
 * An event that should be triggered.
 *
 * @param string $hookname      The name of the event to raise
 * @param array  $args          Arguments that should be passed to the event handler
 * @param bool   $allowinactive Allow inactive modules
 * @param bool   $only          Only this module?
 *
 * @return array The args modified by the event handlers
 */
$currenthook = '';
/**
 * @deprecated 4.4.0 Remove in future version.
 */
function modulehook($hookname, $args = false, $allowinactive = false, $only = false)
{
    global $navsection, $mostrecentmodule;
    global $blocked_modules, $block_all_modules, $unblocked_modules;
    global $session, $modulehook_queries;
    global $currenthook;

    @trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.4.0; and delete in future version.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $lasthook           = $currenthook;
    $currenthook        = $hookname;
    static $hookcomment = [];

    $args = $args ?: [];

    if ( ! \is_iterable($args))
    {
        $where = $mostrecentmodule;

        if ( ! $where)
        {
            $where = \LotgdRequest::getServer('SCRIPT_NAME');
        }
        \LotgdResponse::pageDebug("Args parameter to modulehook {$hookname} from {$where} is not an iterable value.");
    }

    if (isset($session['user']['superuser']) && $session['user']['superuser'] & SU_DEBUG_OUTPUT && ! isset($hookcomment[$hookname]))
    {
        bdump($args, \sprintf(
            'Module hook: %s; allow inactive: (%s); only this module: (%s)',
            $hookname,
            ($allowinactive ? 'true' : 'false'),
            (false !== $only ? $only : 'any module')
        ));

        $hookcomment[$hookname] = true;
    }

    $result = [];

    if (\Doctrine::isConnected())
    {
        $repository = \Doctrine::getRepository('LotgdCore:ModuleHooks');
        $query      = $repository->createQueryBuilder('u');

        $query
            ->leftJoin('LotgdCore:Modules', 'm', 'with', $query->expr()->eq('m.modulename', 'u.modulename'))
            ->where('u.location = :loc')
            ->setParameter('loc', $hookname)
            ->orderBy('u.priority')
            ->addOrderBy('u.modulename')
        ;

        if ( ! $allowinactive)
        {
            $query->andWhere('m.active = 1');
        }

        $result = $query->getQuery()->getArrayResult();
    }

    // $args is an array passed by value and we take the output and pass it
    // back through
    // Try at least and fix up a bogus arg so it doesn't cause additional
    // problems later.
    if ( ! \is_iterable($args))
    {
        $args = ['bogus_args' => $args];
    }

    // Save off the mostrecent module since having that change can change
    // behaviour especially if a module calls modulehooks itself or calls
    // library functions which cause them to be called.
    $mod = $mostrecentmodule;

    foreach ($result as $row)
    {
        // If we are only running hooks for a specific module, skip all
        // others.
        if (false !== $only && $row['modulename'] != $only)
        {
            continue;
        }
        // Skip any module invocations which should be blocked.

        if ( ! \array_key_exists($row['modulename'], $blocked_modules))
        {
            $blocked_modules[$row['modulename']] = false;
        }

        if ( ! \array_key_exists($row['modulename'], $unblocked_modules))
        {
            $unblocked_modules[$row['modulename']] = false;
        }

        if (($block_all_modules || $blocked_modules[$row['modulename']]) && ! $unblocked_modules[$row['modulename']])
        {
            continue;
        }

        if ( ! injectmodule($row['modulename'], $allowinactive))
        {
            continue;
        }

        $oldnavsection = $navsection;
        // Pass the args into the function and reassign them to the
        // result of the function.
        // Note: each module gets the previous module's modified return
        // value if more than one hook here.
        // Order of operations could become an issue, modules are called
        // in alphabetical order by their module name (not display name).

        // Test the condition code
        if ( ! \array_key_exists('whenactive', $row))
        {
            $row['whenactive'] = '';
        }
        $cond = \trim($row['whenactive']);

        if ('' == $cond || module_condition($cond))
        {
            // call the module's hook code
            //before, this was just string switching, NOW we make new objects everytime Oo craaaazy load, I am removing this. if you want to collapse, put it in, it's a MODULE

            $starttime = \microtime(true);

            if (\function_exists($row['function']))
            {
                $res = $row['function']($hookname, $args);
            }
            else
            {
                @trigger_error("Unknown function {$row['function']} for hookname {$hookname} in module {$row['module']}.", E_USER_WARNING);
            }

            $endtime = \microtime(true);

            if ($endtime - $starttime >= 1.00 && ($session['user']['superuser'] & SU_DEBUG_OUTPUT))
            {
                \LotgdResponse::pageDebug('Slow Hook ('.\round($endtime - $starttime, 2)."s): {$hookname} - {$row['modulename']}`n");
            }

            if (LotgdSetting::getSetting('debug', 0))
            {
                $repository = \Doctrine::getRepository('LotgdCore:Debug');

                $entity = $repository->hydrateEntity([
                    'type'        => 'hooktime',
                    'category'    => $hookname,
                    'subcategory' => $row['modulename'],
                    'value'       => ($endtime - $starttime),
                ]);

                \Doctrine::persist($entity);
            }

            // test to see if we had any output and if the module allows
            // us to collapse it

            if ( ! \is_iterable($res))
            {
                @trigger_error("<strong>{$row['function']}<strong> did not return an iterable in the module <strong>{$row['modulename']}</b> for hook <strong>{$hookname}</strong>.", E_USER_WARNING);
                $res = $args;
            }

            // Clear the collapse flag
            if (isset($res['nocollapse']))
            {
                unset($res['nocollapse']);
            }
            //handle return arguments.
            if (\is_iterable($res))
            {
                $args = $res;
            }
        }

        //revert nav section after we're done here.
        $navsection = $oldnavsection;
    }

    $mostrecentmodule = $mod;
    $currenthook      = $lasthook;

    // And hand them back so they can be used.
    return (array) $args;
}

/**
 * Delete hooks of module.
 *
 * @deprecated 4.4.0 Remove in future version.
 */
function module_wipehooks(string $module)
{
    @trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.4.0; and delete in future version.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $delHooks = \Doctrine::createQueryBuilder();
    $delHooks->where('u.modulename = :name')
        ->setParameter('name', $module)
    ;
    $delHooksEvents = clone $delHooks;

    try
    {
        \LotgdResponse::pageDebug("Removing all hooks for {$module}");

        $delHooks->delete('LotgdCore:ModuleHooks', 'u')
            ->getQuery()
            ->execute()
        ;
        $delHooksEvents->delete('LotgdCore:ModuleEventHooks', 'u')
            ->getQuery()
            ->execute()
        ;
    }
    catch (\Throwable $ex)
    {
        \Tracy\Debugger::log($ex);
    }
}

function module_addeventhook($type, $chance)
{
    global $mostrecentmodule;

    \LotgdResponse::pageDebug("Adding an event hook on {$type} events for {$mostrecentmodule}");

    $repository = \Doctrine::getRepository('LotgdCore:ModuleEventHooks');
    $entity     = $repository->findOneBy(['modulename' => $mostrecentmodule, 'eventType' => $type]);

    $entity = $repository->hydrateEntity([
        'eventType'   => $type,
        'modulename'  => $mostrecentmodule,
        'eventChance' => $chance,
    ], $entity);

    \Doctrine::persist($entity);

    \Doctrine::flush();
}

/**
 * @deprecated 4.4.0 Remove in future version
 *
 * @param [type] $hookname
 * @param bool $functioncall
 * @return void
 */
function module_drophook($hookname, $functioncall = false)
{
    global $mostrecentmodule;

    @trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.4.0; and delete in future version.',
        __METHOD__
    ), E_USER_DEPRECATED);

    if (false === $functioncall)
    {
        $functioncall = "{$mostrecentmodule}_dohook";
    }

    $repository = \Doctrine::getRepository('LotgdCore:ModuleHooks');
    $result     = $repository->findBy(['modulename' => $mostrecentmodule, 'location' => $hookname, 'function' => $functioncall]);

    foreach ($result as $row)
    {
        \Doctrine::remove($row);
    }

    \Doctrine::flush();
}

/**
 * Called by modules to register themselves for a game module hook point, with default priority.
 * Modules with identical priorities will execute alphabetically.  Modules can only have one hook on a given hook name,
 * even if they call this function multiple times, unless they specify different values for the functioncall argument.
 *
 * @param string $hookname     The hook to receive a notification for
 * @param string $functioncall The function that should be called, if not specified, use {modulename}_dohook() as the function
 * @param string $whenactive   an expression that should be evaluated before triggering the event, if not specified, none
 *
 * @deprecated 4.4.0 Remove in future version
 */
function module_addhook($hookname, $functioncall = false, $whenactive = false)
{
    @trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.4.0; and delete in future version.',
        __METHOD__
    ), E_USER_DEPRECATED);

    module_addhook_priority($hookname, 50, $functioncall, $whenactive);
}

/**
 * Called by modules to register themselves for a game module hook point, with a given priority -- lower numbers execute first.
 * Modules with identical priorities will execute alphabetically.  Modules can only have one hook on a given hook name,
 * even if they call this function multiple times, unless they specify different values for the functioncall argument.
 *
 * @param string $hookname     The hook to receive a notification for
 * @param int    $priority     The priority for this hooking -- lower numbers execute first.  < 50 means earlier-than-normal execution, > 50 means later than normal execution.  Priority only affects execution order compared to other events registered on the same hook, all events on a given hook will execute before the game resumes execution.
 * @param string $functioncall The function that should be called, if not specified, use {modulename}_dohook() as the function
 * @param string $whenactive   an expression that should be evaluated before triggering the event, if not specified, none
 *
 * @deprecated 4.4.0 Remove in future version
 */
function module_addhook_priority($hookname, $priority = 50, $functioncall = false, $whenactive = false)
{
    global $mostrecentmodule;

    @trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.4.0; and delete in future version.',
        __METHOD__
    ), E_USER_DEPRECATED);

    module_drophook($hookname, $functioncall);

    if (false === $functioncall)
    {
        $functioncall = "{$mostrecentmodule}_dohook";
    }

    if (false === $whenactive)
    {
        $whenactive = '';
    }

    \LotgdResponse::pageDebug("Adding a hook at {$hookname} for {$mostrecentmodule} to {$functioncall} which is active on condition '{$whenactive}'");
    //we want to do a replace in case there's any garbage left in this table which might block new clean data from going in.
    //normally that won't be the case, and so this doesn't have any performance implications.
    $repository = \Doctrine::getRepository('LotgdCore:ModuleHooks');
    $entity     = $repository->findBy(['modulename' => $mostrecentmodule, 'location' => $hookname, 'function' => $functioncall]);

    $entity = $repository->hydrateEntity([
        'modulename' => $mostrecentmodule,
        'location'   => $hookname,
        'function'   => $functioncall,
        'whenactive' => $whenactive,
        'priority'   => $priority,
    ], $entity);

    \Doctrine::persist($entity);
    \Doctrine::flush();
}
