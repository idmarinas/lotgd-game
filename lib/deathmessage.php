<?php

// translator ready
// addnews ready
// mail ready
require_once 'lib/class/dbwrapper.php';
require_once 'lib/e_rand.php';
require_once 'lib/substitute.php';

function select_deathmessage($forest = true, $extra = [], $extrarep = [])
{
    global $session, $badguy;

    $where = ($forest ? 'WHERE forest=1' : 'WHERE graveyard=1');

    $sql = 'SELECT deathmessage,taunt FROM '.DB::prefix('deathmessages')." $where ORDER BY rand(".e_rand().') LIMIT 1';

    $result = DB::query($sql);

    if ($result)
    {
        $row = DB::fetch_assoc($result);
        $deathmessage = $row['deathmessage'];
        $taunt = $row['taunt'];
    }
    else
    {
        $taunt = 1;
        $deathmessage = "`5\"`6{goodguyname}'s mother wears combat boots`5\", screams {badguyname}.";
    }

    $deathmessage = substitute($deathmessage, $extra, $extrarep);

    return ['deathmessage' => $deathmessage, 'taunt' => $taunt];
}

function select_deathmessage_array($forest = true, $extra = [], $extrarep = [])
{
    global $session, $badguy;

    $where = ($forest ? 'WHERE forest=1' : 'WHERE graveyard=1');

    $sql = 'SELECT deathmessage, taunt FROM '.DB::prefix('deathmessages')." $where ORDER BY rand(".e_rand().') LIMIT 1';

    $result = DB::query($sql);

    if ($result)
    {
        $row = DB::fetch_assoc($result);
        $deathmessage = $row['deathmessage'];
        $taunt = $row['taunt'];
    }
    else
    {
        $taunt = 1;
        $deathmessage = "`5\"`6{goodguyname}'s mother wears combat boots`5\", screams {badguyname}.";
    }

    if ('{where}' == $extra[0])
    {
        $deathmessage = str_replace($extra[0], $extrarep[0], $deathmessage);
    }
    $deathmessage = substitute_array($deathmessage, $extra, $extrarep);
    array_unshift($deathmessage, true, 'deathmessages');

    return ['deathmessage' => $deathmessage, 'taunt' => $taunt];
}
