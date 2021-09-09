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

trait Substitute
{
    function substitute(?string $string, ?array $extraSearch = null, ?array $extraReplace = null)
    {
        global $session;

        $search = [
            '{himher}',
            '{heshe}',
            '{hisher}',
            '{goodguyweapon}',
            '{goodguyarmor}',
            '{goodguyname}',
            '{goodguy}',
            '{weapon}',
            '{armor}',
        ];

        $replace = [
            ($session['user']['sex'] ? 'her' : 'him'),
            ($session['user']['sex'] ? 'she' : 'he'),
            ($session['user']['sex'] ? 'her' : 'his'),
            $session['user']['weapon'],
            $session['user']['armor'],
            '`^'.$session['user']['name'].'`0',
            '`^'.$session['user']['name'].'`0',
            $session['user']['weapon'],
            $session['user']['armor'],
        ];

        if (is_array($extraSearch) && is_array($extraReplace))
        {
            $search  = \array_merge($search, $extraSearch);
            $replace = \array_merge($replace, $extraReplace);
        }

        return \str_replace($search, $replace, $string);
    }

    function substituteArray(?string $string, ?array $extraSearch = null, ?array $extraReplace = null)
    {
        global $session;
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
        $string = \str_replace($search, $replace, $string);

        $search = [
            '{goodguyweapon}',
            '{goodguyarmor}',
            '{goodguyname}',
            '{goodguy}',
            '{weapon}',
            '{armor}',
        ];

        $replace = [
            $session['user']['weapon'],
            $session['user']['armor'],
            '`^'.$session['user']['name'].'`0',
            '`^'.$session['user']['name'].'`0',
            $session['user']['weapon'],
            $session['user']['armor'],
        ];

        if (is_array($extraSearch) && is_array($extraReplace))
        {
            $search  = \array_merge($search, $extraSearch);
            $replace = \array_merge($replace, $extraReplace);
        }
        $replacement_array = [$string];

        // Do this the right way.
        // Iterate the string and find the replacements in order
        $length = \strlen($replacement_array[0]);

        for ($x = 0; $x < $length; ++$x)
        {
            \reset($search);

            foreach ($search as $skey => $sval)
            {
                // Get the replacement for this value.
                $rval = $replace[$skey];

                if (\substr($replacement_array[0], $x, \strlen($sval)) == $sval)
                {
                    $replacement_array[] = $rval;
                    $replacement_array[0] = \substr($replacement_array[0], 0, $x).'%s'.
                        \substr($replacement_array[0], $x + \strlen($sval));
                    // Making a replacement changes the length, so we need to
                    // restart at the beginning of the string.
                    $x = -1;

                    break;
                }
            }
        }

        return $replacement_array;
    }
}
