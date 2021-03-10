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

namespace Lotgd\Bundle\SettingsBundle\Doctrine\FieldEnum;

use MyCLabs\Enum\Enum;

/**
 * Enum Type.
 */
class SettingType extends Enum
{
    public const STRING = 'string';
    public const BOOL   = 'bool';
    public const INT    = 'int';
    public const FLOAT  = 'float';
}
