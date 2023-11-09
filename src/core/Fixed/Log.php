<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.3.0
 */

namespace Lotgd\Core\Fixed;

use function class_alias;

/**
 * @method static void game(string $message, string $category = 'general', bool $filed = false)
 * @method static void debug(string $message, ?int $target = null, ?int $user = null, ?string $field = null, ?int $value = null, bool $consolidate = true)
 */
class Log
{
    use StaticTrait;
}

class_alias('Lotgd\Core\Fixed\Log', 'LotgdLog', false);
