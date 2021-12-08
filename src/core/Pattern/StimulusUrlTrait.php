<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 7.0.0
 */

namespace Lotgd\Core\Pattern;

trait StimulusUrlTrait
{
    public function getStimulusUrl(string $controller, string $method = 'index', string $query = ''): string
    {
        return "stimulus.php?method={$method}&controller=".urlencode($controller).$query;
    }
}

