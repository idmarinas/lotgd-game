<?php

// translator ready
// addnews ready
// mail ready
require_once 'lib/class/static.php';
require_once 'lib/e_rand.php';
require_once 'lib/substitute.php';

/**
 * Select 1 death message.
 *
 * @param string $zone
 * @param array  $extraParams
 * @param array  $extrarep
 *
 * @return array
 */
function select_deathmessage($zone = 'forest', $extraParams = []): array
{
    global $session, $badguy;

    $count = \LotgdTranslator::st("{$zone}.count", 'partial-deathmessage');

    //-- Default message, if fail in count
    //-- Always shows the default message if the key is not found in the specified language when translating.
    $deathmessage = 'default';
    //-- Check if found count for zone
    if ("{$zone}.count" != $count)
    {
        $rand = mt_rand(0, max(0, $count - 1));

        $deathmessage = "{$zone}.0{$rand}";
    }

    $params = [
        //-- The player's name (also can be specified as goodGuy
        'goodGuyName' => $session['user']['name'],
        'goodGuy' => $session['user']['name'],
        //-- The player's weapon (also can be specified as weapon
        'goodGuyWeapon' => $session['user']['weapon'],
        'weapon' => $session['user']['weapon'],
        //-- The player's armor (also can be specified as armor
        'armorName' => $session['user']['armor'],
        'armor' => $session['user']['armor'],
        //-- Subjective pronoun for the player (him her)
        'himHer' => $session['user']['sex'] ? 'her' : 'him',
        //-- Possessive pronoun for the player (his her)
        'hisHer' => $session['user']['sex'] ? 'her' : 'his',
        //-- Objective pronoun for the player (he she)
        'heShe' => $session['user']['sex'] ? 'she' : 'he',
        //-- The monster's name (also can be specified as badGuy
        'badGuyName' => $badguy['creaturename'],
        'badGuy' => $badguy['creaturename'],
        //-- The monster's weapon (also can be specified as creatureWeapon
        'badGuyWeapon' => $badguy['creatureweapon'],
        'creatureWeapon' => $badguy['creatureweapon']
    ];

    $params = \array_merge($params, $extraParams);

    return [
        'deathmessage' => $deathmessage,
        'params' => $params,
        'textDomain' => 'partial-deathmessage'
    ];
}
