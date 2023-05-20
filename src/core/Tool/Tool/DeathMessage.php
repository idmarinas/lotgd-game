<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 5.4.0
 */

namespace Lotgd\Core\Tool\Tool;

trait DeathMessage
{
    /**
     * Select 1 death message.
     */
    public function selectDeathMessage(string $zone = 'forest', array $extraParams = []): array
    {
        global $session;

        $count = $this->translator->trans("{$zone}.count", [], 'partial_deathmessage');

        //-- Default message, if fail in count
        //-- Always shows the default message if the key is not found in the specified language when translating.
        $deathmessage = 'default';
        //-- Check if found count for zone
        if ("{$zone}.count" != $count)
        {
            $rand = \mt_rand(0, \max(0, $count - 1));

            $deathmessage = "{$zone}.0{$rand}";
        }

        $params = [
            //-- The player's name (also can be specified as goodGuy
            'goodGuyName' => $session['user']['name'],
            'goodGuy'     => $session['user']['name'],
            //-- The player's weapon (also can be specified as weapon
            'goodGuyWeapon' => $session['user']['weapon'],
            'weapon'        => $session['user']['weapon'],
            //-- The player's armor (also can be specified as armor
            'armorName' => $session['user']['armor'],
            'armor'     => $session['user']['armor'],
            //-- Subjective pronoun for the player (him her)
            'himHer' => $session['user']['sex'] ? 'her' : 'him',
            //-- Possessive pronoun for the player (his her)
            'hisHer' => $session['user']['sex'] ? 'her' : 'his',
            //-- Objective pronoun for the player (he she)
            'heShe' => $session['user']['sex'] ? 'she' : 'he',
        ];

        $params = \array_merge($params, $extraParams);

        return [
            'deathmessage' => $deathmessage,
            'params'       => $params,
            'textDomain'   => 'partial_deathmessage',
        ];
    }
}
