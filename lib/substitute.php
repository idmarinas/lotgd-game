<?php

// translator ready
// addnews ready
// mail ready
function substitute($string, $extra = false, $extrarep = false)
{
    global $badguy, $session;

    $search = [
        '{himher}',
        '{heshe}',
        '{hisher}',
        '{goodguyweapon}',
        '{badguyweapon}',
        '{goodguyarmor}',
        '{badguyname}',
        '{goodguyname}',
        '{badguy}',
        '{goodguy}',
        '{weapon}',
        '{armor}',
        '{creatureweapon}',
    ];
    $replace = [translate_inline($session['user']['sex'] ? 'her' : 'him', 'buffs'),
        ($session['user']['sex'] ? 'she' : 'he'),
        ($session['user']['sex'] ? 'her' : 'his'),
        $session['user']['weapon'],
        $badguy['creatureweapon'],
        $session['user']['armor'],
        $badguy['creaturename'],
        '`^'.$session['user']['name'].'`^',
        $badguy['creaturename'],
        '`^'.$session['user']['name'].'`^',
        $session['user']['weapon'],
        $session['user']['armor'],
        $badguy['creatureweapon'],
    ];

    if (false !== $extra && false !== $extrarep)
    {
        $search = array_merge($search, $extra);
        $replace = array_merge($replace, $extrarep);
    }

    $string = str_replace($search, $replace, $string);

    return $string;
}

function substitute_array($string, $extra = false, $extrarep = false)
{
    global $badguy, $session;
    // separate substitutions for gender items (makes 2 translations per
    // substition that uses these)
    $search = [
        '{himher}',
        '{heshe}',
        '{hisher}',
    ];

    $replace = [
        ($session['user']['sex'] ? 'her' : 'him'),
        ($session['user']['sex'] ? 'she' : 'he'),
        ($session['user']['sex'] ? 'her' : 'his'),
    ];
    $string = str_replace($search, $replace, $string);

    $search = [
        '{goodguyweapon}',
        '{badguyweapon}',
        '{goodguyarmor}',
        '{badguyname}',
        '{goodguyname}',
        '{badguy}',
        '{goodguy}',
        '{weapon}',
        '{armor}',
        '{creatureweapon}',
    ];

    $replace = [
        $session['user']['weapon'],
        $badguy['creatureweapon'],
        $session['user']['armor'],
        $badguy['creaturename'],
        '`^'.$session['user']['name'].'`^',
        $badguy['creaturename'],
        '`^'.$session['user']['name'].'`^',
        $session['user']['weapon'],
        $session['user']['armor'],
        $badguy['creatureweapon'],
    ];

    if (false !== $extra && false !== $extrarep)
    {
        $search = array_merge($search, $extra);
        $replace = array_merge($replace, $extrarep);
    }
    $replacement_array = [$string];

    // Do this the right way.
    // Iterate the string and find the replacements in order
    $length = strlen($replacement_array[0]);

    for ($x = 0; $x < $length; $x++)
    {
        reset($search);

        foreach ($search as $skey => $sval)
        {
            // Get the replacement for this value.
            $rval = $replace[$skey];

            if (substr($replacement_array[0], $x, strlen($sval)) == $sval)
            {
                array_push($replacement_array, $rval);
                $replacement_array[0] =
                    substr($replacement_array[0], 0, $x).'%s'.
                    substr($replacement_array[0], $x + strlen($sval));
                // Making a replacement changes the length, so we need to
                // restart at the beginning of the string.
                $x = -1;
                break;
            }
        }
    }

    return $replacement_array;
}
