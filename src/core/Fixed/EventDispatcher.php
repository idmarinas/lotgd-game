<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.2.0
 */

namespace Lotgd\Core\Fixed;

use Symfony\Contracts\EventDispatcher\Event;
use function class_alias;

/**
 * @method static Event dispatch($event, string $eventName = null)
 */
class EventDispatcher
{
    use StaticTrait;
}

class_alias('Lotgd\Core\Fixed\EventDispatcher', 'LotgdEventDispatcher', false);
