<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.5.0
 */

namespace Lotgd\Core\Fixed;

/**
 * @method static mixed getSetting($settingname, $default = null)
 * @method static bool saveSetting(string $settingname, $value)
 */
class Setting
{
    use StaticTrait;
}

\class_alias('Lotgd\Core\Fixed\Setting', 'LotgdSetting', false);
