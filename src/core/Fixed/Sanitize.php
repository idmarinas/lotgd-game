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
 * This class is for sanitize a string
 * For example: sanitize a number or a date.
 *
 * @method static string unColorize(string $string)
 * @method static string noLotgdCodes(string $string)
 * @method static string fullSanitize(string $string)
 * @method static string preventLotgdCodes(string $string)
 * @method static string moduleNameSanitize(string $string)
 * @method static string nameSanitize($spaceAllowed, string $name)
 * @method static string colorNameSanitize($spaceallowed, string $string, $admin = null)
 * @method static string htmlSanitize(string $string)
 */
class Sanitize
{
    use StaticTrait;
}

\class_alias('Lotgd\Core\Fixed\Sanitize', 'LotgdSanitize', false);
