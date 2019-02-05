<?php

// translator ready
// addnews ready
// mail ready
function fightnav($allowspecial = true, $allowflee = true, $script = false)
{
    global $session, $newenemies, $companions;

    tlschema('fightnav');

    if (false === $script)
    {
        $PHP_SELF = LotgdHttp::getServer('PHP_SELF');
        $script = substr($PHP_SELF, strrpos($PHP_SELF, '/') + 1).'?';
    }
    elseif (false === strpos($script, '?'))
    {
        $script .= '?';
    }
    elseif ('&' != substr($script, strlen($script) - 1))
    {
        $script .= '&';
    }

    $fight = 'Fight';
    $run = 'Run';

    if (! $session['user']['alive'])
    {
        $fight = 'F?Torment';
        $run = 'R?Flee';
    }
    modulehook('fightnav-prenav', ['script' => $script]);
    addnav('Standard Fighting');
    addnav($fight, $script.'op=fight');

    if ($allowflee)
    {
        addnav($run, $script.'op=run');
    }

    if ($session['user']['superuser'] & SU_DEVELOPER)
    {
        addnav('Abort', $script);
    }

    if (getsetting('autofight', 0))
    {
        addnav('Automatic Fighting');
        addnav('5?For 5 Rounds', $script.'op=fight&auto=five');
        addnav('1?For 10 Rounds', $script.'op=fight&auto=ten');
        $auto = getsetting('autofightfull', 0);

        if ((1 == $auto || (2 == $auto && ! $allowflee)) && 1 == count($newenemies))
        {
            addnav('U?Until End', $script.'op=fight&auto=full');
        }
        elseif (1 == $auto || (2 == $auto && ! $allowflee))
        {
            addnav('U?Until current enemy dies', $script.'op=fight&auto=full');
        }
    }

    //added hook for the Stamina system
    if (! $session['user']['alive'])
    {
        modulehook('fightnav-graveyard', ['script' => $script]);
    }

    if ($allowspecial)
    {
        addnav('Special Abilities');
        modulehook('fightnav-specialties', ['script' => $script]);

        if ($session['user']['superuser'] & SU_DEVELOPER)
        {
            addnav('`&Super user`0', '');
            addnav('!?`&&#149; __GOD MODE`0', "{$script}op=fight&skill=godmode", true);
        }
        modulehook('fightnav', ['script' => $script]);
    }

    if (count($newenemies) > 1)
    {
        addnav('Targets');

        foreach ($newenemies as $index => $badguy)
        {
            if ($badguy['creaturehealth'] <= 0 || (isset($badguy['dead']) && true == $badguy['dead']))
            {
                continue;
            }
            addnav_notl(['%s%s`0', (isset($badguy['istarget']) && $badguy['istarget']) ? '`#*`0' : '', $badguy['creaturename']], $script."op=fight&newtarget=$index");
        }
    }
    tlschema();
}
