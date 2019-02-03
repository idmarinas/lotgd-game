<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension\Pattern;

/**
 * Trait to created message of the day link.
 */
trait Motd
{
    /**
     * Get message of the day link.
     *
     * @return string
     */
    function messageOfTheDay(): string
    {
        global $session;

        return \LotgdTheme::renderThemeTemplate('parts/motd.twig', ['newMotd' => $session['needtoviewmotd']]);
    }
}
