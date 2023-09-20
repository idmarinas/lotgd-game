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

namespace Lotgd\Core\Fixed;

/**
 * @method static $this addNav(?string $label, ?string $link = null, array $options = [])
 * @method static $this addHeader(string $header, array $options = [])
 * @method static $this addNavNotl(?string $label, ?string $link = null, array $options = [])
 * @method static $this blockLink(string $link)
 */
class Navigation
{
    use StaticTrait;
}

\class_alias('Lotgd\Core\Fixed\Navigation', 'LotgdNavigation', false);
