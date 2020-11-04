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
 * Trait to created source link.
 */
trait Source
{
    /**
     * Get source link.
     */
    public function gameSource(): string
    {
        return $this->getTemplate()->renderBlock('game_source', '{theme}/_blocks/_buttons.html.twig', []);
    }
}
