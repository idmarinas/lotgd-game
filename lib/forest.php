<?php

// addnews ready
// translator ready
// mail ready
require_once 'lib/villagenav.php';

function forest($noshowmessage = false)
{
    global $session, $playermount;

    tlschema('forest');
    //	mass_module_prepare(array("forest", "validforestloc"));
    addnav('Navigation');
    villagenav();
    addnav('Heal');
    addnav("H?Healer's Hut", 'healer.php');
    addnav('Fight');
    addnav('L?Look for Something to Kill', 'forest.php?op=search');

    if ($session['user']['level'] > 1)
    {
        addnav('S?Go Slumming', 'forest.php?op=search&type=slum');
    }
    addnav('T?Go Thrillseeking', 'forest.php?op=search&type=thrill');

    if (getsetting('suicide', 0))
    {
        if (getsetting('suicidedk', 10) <= $session['user']['dragonkills'])
        {
            addnav('*?Search `$Suicidally`0', 'forest.php?op=search&type=suicide');
        }
    }
    addnav('Other');
    modulehook('forest-header');

    if ($session['user']['level'] >= getsetting('maxlevel', 15) && 0 == $session['user']['seendragon'])
    {
        // Only put the green dragon link if we are a location which
        // should have a forest.   Don't even ask how we got into a forest()
        // call if we shouldn't have one.   There is at least one way via
        // a superuser link, but it shouldn't happen otherwise.. We just
        // want to make sure however.
        $isforest = 0;
        $vloc = modulehook('validforestloc', []);

        foreach ($vloc as $i => $l)
        {
            if ($session['user']['location'] == $i)
            {
                $isforest = 1;
                break;
            }
        }

        if ($isforest || 0 == count($vloc))
        {
            addnav('G?`@Seek Out the Green Dragon', 'forest.php?op=dragon');
        }
    }

    if (true != $noshowmessage)
    {
        output('`c`7`bThe Forest`b`0´c');
        output('The Forest, home to evil creatures and evildoers of all sorts.`n`n');
        output('The thick foliage of the forest restricts your view to only a few yards in most places.');
        output('The paths would be imperceptible except for your trained eye.');
        output('You move as silently as a soft breeze across the thick moss covering the ground, wary to avoid stepping on a twig or any of the numerous pieces of bleached bone that populate the forest floor, lest you betray your presence to one of the vile beasts that wander the forest.`n');
        modulehook('forest-desc');
    }
    modulehook('forest', []);
    module_display_events('forest', 'forest.php');
    tlschema();
}
