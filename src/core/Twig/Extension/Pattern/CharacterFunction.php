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

trait CharacterFunction
{
    /**
     * Get character race name.
     *
     * @return string
     */
    public function characterRace(): string
    {
        global $session;

        return $this->getTranslator()->trans('character.racename', [], $session['user']['race']);
    }
}
