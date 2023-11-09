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

use function class_alias;

/**
 * @method static $this addNav(?string $label, ?string $link = null, array $options = [])
 * @method static $this addNavNotl(?string $label, ?string $link = null, array $options = [])
 * @method static $this addHeader(string $header, array $options = [])
 * @method static $this addHeaderNotl(string $header, array $options = [])
 * @method static $this addNavAllow(string $link)
 * @method static $this addNavExternal(?string $label, ?string $link = null, array $options = [])
 * @method static $this blockLink(string $link)
 * @method static $this unBlockLink(string $link)
 * @method static $this setTextDomain(?string $domain = null)
 * @method static void villageNav($extra = '')
 * @method static void forestNav(string $translationDomain)
 * @method static void graveyardNav(string $translationDomain)
 */
class Navigation
{
    use StaticTrait;
}

class_alias('Lotgd\Core\Fixed\Navigation', 'LotgdNavigation', false);
