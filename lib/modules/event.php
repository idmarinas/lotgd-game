<?php

function module_collect_events($type, $allowinactive = false)
{
    global $session, $blocked_modules, $block_all_modules, $unblocked_modules;

    $events = [];

    $select = DB::select(['e' => 'module_event_hooks']);
    $select->columns(['*'])
        ->join(['m' => DB::prefix('modules')], 'm.modulename = e.modulename')
        ->order(DB::expression('RAND('.e_rand().')'))
        ->where->equalTo('event_type', $type)
    ;

    if (! $allowinactive)
    {
        $select->where->equalTo('active', 1);
    }

    $result = DB::execute($select);

    while ($row = DB::fetch_assoc($result))
    {
        // The event_chance bit needs to return a value, but it can do that
        // in any way it wants, and can have if/then or other logical
        // structures, so we cannot just force the 'return' syntax unlike
        // with buffs.
        ob_start();
        $chance = eval($row['event_chance'].';');
        $err = ob_get_contents();
        ob_end_clean();

        if ($err > '')
        {
            debug(['error' => $err, 'Eval code' => $row['event_chance']]);
        }

        $chance = max(0, min(100, $chance));

        if (($block_all_modules || array_key_exists($row['modulename'], $blocked_modules)
            && $blocked_modules[$row['modulename']])
            && (! array_key_exists($row['modulename'], $unblocked_modules) || ! $unblocked_modules[$row['modulename']]))
        {
            $chance = 0;
        }
        $events[] = ['modulename' => $row['modulename'], 'rawchance' => $chance];
    }

    // Now, normalize all of the event chances
    $sum = 0;
    reset($events);

    foreach ($events as $event)
    {
        $sum += $event['rawchance'];
    }
    reset($events);

    foreach ($events as $index => $event)
    {
        if (0 == $sum)
        {
            $events[$index]['normchance'] = 0;
        }
        else
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
    if (false === $baseLink)
    {
        global $PHP_SELF;

        $baseLink = substr($PHP_SELF, strrpos($PHP_SELF, '/') + 1).'?';
    }

    if (e_rand(1, 100) <= $basechance)
    {
        global $PHP_SELF;
        $events = module_collect_events($eventtype);
        $chance = r_rand(1, 100);
        reset($events);
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
                tlschema('events');
                output('`^`c`bSomething Special!´c´b`0');
                tlschema();
                $op = httpget('op');
                httpset('op', '');
                module_do_event($eventtype, $event['modulename'], false, $baseLink);
                httpset('op', $op);

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
        global $PHP_SELF;

        $baseLink = substr($PHP_SELF, strrpos($PHP_SELF, '/') + 1).'?';
    }

    // Save off the mostrecent module since having that change can change
    // behaviour especially if a module calls modulehooks itself or calls
    // library functions which cause them to be called.
    if (! isset($mostrecentmodule))
    {
        $mostrecentmodule = '';
    }
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
        modulehook("runevent_$module", ['type' => $type, 'baselink' => $baseLink, 'get' => httpallget(), 'post' => httpallpost()]);
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
    global $PHP_SELF, $session;

    if (! ($session['user']['superuser'] & SU_DEVELOPER))
    {
        return;
    }

    $script = $forcescript;
    if (false === $forcescript)
    {
        $script = substr($PHP_SELF, strrpos($PHP_SELF, '/') + 1);
    }

    $events = module_collect_events($eventtype, true);

    if (! is_array($events) || 0 == count($events))
    {
        return;
    }

    usort($events, 'event_sort');

    tlschema('events');
    output('`n`nSpecial event triggers:`n');
    $name = translate_inline('Name');
    $rchance = translate_inline('Raw Chance');
    $nchance = translate_inline('Normalized Chance');
    rawoutput("<table class='ui small very compact striped selectable table'>");
    rawoutput('<thead><tr>');
    rawoutput("<th>$name</th><th>$rchance</th><th>$nchance</th>");
    rawoutput('</tr></thead>');
    $i = 0;

    foreach ($events as $event)
    {
        // Each event is an associative array of 'modulename',
        // 'rawchance' and 'normchance'
        $i++;

        if ($event['modulename'])
        {
            $link = "module-{$event['modulename']}";
            $name = $event['modulename'];
        }
        $rlink = "$script?eventhandler=$link";
        $rlink = str_replace('?&', '?', $rlink);
        $first = strpos($rlink, '?');
        $rl1 = substr($rlink, 0, $first + 1);
        $rl2 = substr($rlink, $first + 1);
        $rl2 = str_replace('?', '&', $rl2);
        $rlink = $rl1.$rl2;
        rawoutput("<tr><td><a href='$rlink'>$name</a></td>");
        addnav('', "$rlink");
        rawoutput("<td>{$event['rawchance']}</td>");
        rawoutput("<td>{$event['normchance']}</td>");
        rawoutput('</tr>');
    }
    rawoutput('</table>');
}
