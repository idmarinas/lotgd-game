<?php

function module_collect_events($type, $allowinactive = false)
{
    global $session, $blocked_modules, $block_all_modules, $unblocked_modules;

    $events = [];

    $repository = \Doctrine::getRepository('LotgdCore:ModuleEventHooks');
    $query = $repository->createQueryBuilder('u');

    $query
        ->leftJoin('LotgdCore:Modules', 'm', 'with', $query->expr()->eq('m.modulename', 'u.modulename'))
        ->where('u.eventType = :type')
        ->setParameter('type', $type)
        ->orderBy('rand()')
    ;

    if (! $allowinactive)
    {
        $query->andWhere('m.active = 1');
    }

    $result = $query->getQuery()->getArrayResult();

    // Normalize all of the event chances
    $sum = 0;

    foreach ($result as $row)
    {
        // The eventChance bit needs to return a value, but it can do that
        // in any way it wants, and can have if/then or other logical
        // structures, so we cannot just force the 'return' syntax unlike
        // with buffs.
        ob_start();
        $chance = eval($row['eventChance'].';');
        $err = ob_get_contents();
        ob_end_clean();

        if ($err > '')
        {
            debug(['error' => $err, 'Eval code' => $row['eventChance']]);
        }

        $chance = max(0, min(100, $chance));

        if (($block_all_modules || array_key_exists($row['modulename'], $blocked_modules)
            && $blocked_modules[$row['modulename']])
            && (! array_key_exists($row['modulename'], $unblocked_modules) || ! $unblocked_modules[$row['modulename']]))
        {
            $chance = 0;
        }

        $events[] = [
            'modulename' => $row['modulename'],
            'rawchance' => $chance
        ];

        $sum += $chance;
    }

    foreach ($events as $index => $event)
    {
        $events[$index]['normchance'] = 0;

        if ($sum)
        {
            $events[$index]['normchance'] = round($event['rawchance'] / $sum * 100, 3);
            // If an event requests 1% chance, don't give them more!
            if ($events[$index]['normchance'] > $event['rawchance'])
            {
                $events[$index]['normchance'] = $event['rawchance'];
            }
        }
    }

    return modulehook('collect-events', $events);
}

function module_events($eventtype, $basechance, $baseLink = false)
{
    global $html;

    if (! $baseLink)
    {
        $PHP_SELF = \LotgdHttp::getServer('PHP_SELF');

        $baseLink = substr($PHP_SELF, strrpos($PHP_SELF, '/') + 1).'?';
    }

    if (e_rand(1, 100) <= $basechance)
    {
        $events = module_collect_events($eventtype);
        $chance = r_rand(1, 100);

        $sum = 0;

        foreach ($events as $event)
        {
            if (0 == $event['rawchance'])
            {
                continue;
            }

            if ($chance > $sum && $chance <= $sum + $event['normchance'])
            {
                $_POST['i_am_a_hack'] = 'true';
                $html['event'] = [
                    'title.special',
                    [],
                    'partial-event'
                ];
                $op = \LotgdHttp::getQuery('op');
                \LotgdHttp::setQuery('op', '');
                module_do_event($eventtype, $event['modulename'], false, $baseLink);
                \LotgdHttp::setQuery('op', $op);

                return 1;
            }
            $sum += $event['normchance'];
        }
    }

    return 0;
}

function module_do_event($type, $module, $allowinactive = false, $baseLink = false)
{
    global $navsection;

    if (false === $baseLink)
    {
        $PHP_SELF = LotgdHttp::getServer('PHP_SELF');

        $baseLink = substr($PHP_SELF, strrpos($PHP_SELF, '/') + 1).'?';
    }

    // Save off the mostrecent module since having that change can change
    // behaviour especially if a module calls modulehooks itself or calls
    // library functions which cause them to be called.
    $mostrecentmodule = $mostrecentmodule ?? '';

    $mod = $mostrecentmodule;
    $_POST['i_am_a_hack'] = 'true';

    if (injectmodule($module, $allowinactive))
    {
        $oldnavsection = $navsection;
        tlschema("module-$module");
        $fname = $module.'_runevent';
        $fname($type, $baseLink);
        tlschema();
        //hook into the running event, but only in *this* running event, not in all
        modulehook("runevent_$module", ['type' => $type, 'baselink' => $baseLink, 'get' => \LotgdHttp::getQueryAll(), 'post' => \LotgdHttp::getPostAll()]);
        //revert nav section after we're done here.
        $navsection = $oldnavsection;
    }

    $mostrecentmodule = $mod;
}

function event_sort($a, $b)
{
    return strcmp($a['modulename'], $b['modulename']);
}

function module_display_events($eventtype, $forcescript = false)
{
    global $session;

    if (! ($session['user']['superuser'] & SU_DEVELOPER))
    {
        return;
    }

    $script = $forcescript;
    if (! $forcescript)
    {
        $PHP_SELF = \LotgdHttp::getServer('PHP_SELF');
        $script = substr($PHP_SELF, strrpos($PHP_SELF, '/') + 1);
    }

    $events = module_collect_events($eventtype, true);

    if (! is_array($events) || ! count($events))
    {
        return;
    }

    usort($events, 'event_sort');

    tlschema('events');

    $params = [
        'textDomain' => 'partial-event',
        'events' => $events,
        'script' => $script
    ];

    rawoutput(LotgdTheme::renderLotgdTemplate('core/partial/event-trigger.twig', $params));
}
