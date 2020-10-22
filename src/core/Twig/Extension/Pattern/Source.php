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
        \trigger_error(\sprintf(
            'Usage of %s (game_source() Twig function) is obsolete since 4.5.0; and delete in version 5.0.0, use "{%% block game_source parent() %%}" instead.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $template = $this->getTemplate()->load("@theme{$this->getTemplate()->getThemeNamespace()}/_blocks/_buttons.html.twig");

        return $template->renderBlock('game_source', []);
    }
}
