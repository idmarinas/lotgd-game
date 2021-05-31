<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension\Pattern;

trait CharacterFunction
{
    /**
     * Get character race name.
     */
    public function characterRace(): string
    {
        global $session;

        return $this->translator->trans('character.racename', [], $session['user']['race']);
    }
}
