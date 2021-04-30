<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Doctrine\FieldEnum;

use MyCLabs\Enum\Enum;

/**
 * Enum Type.
 */
class PetitionStatusTypeEnum extends Enum
{
    public const UNHANDLED       = 'unhandled';
    public const IN_PROGRESS     = 'in_progress';
    public const INFORMATIONAL   = 'informational';
    public const ESCALATED       = 'escalated';
    public const TOP_LEVEL       = 'top_level';
    public const BUG             = 'bug';
    public const AWAITING_POINTS = 'awaiting_points';
    public const CLOSED          = 'closed';
}
