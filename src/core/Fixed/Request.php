<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.4.0
 */

namespace Lotgd\Core\Fixed;

use function class_alias;

/**
 * @method static mixed getQuery($name, $default = null)
 * @method static mixed getPost($name, $default = null)
 */
class Request
{
    use StaticTrait;
}

class_alias('Lotgd\Core\Fixed\Request', 'LotgdRequest', false);
