<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 3.0.0
 */

namespace Lotgd\Core\Fixed;

/**
 * This class is for format a string
 * For example: format a number or a date.
 *
 * @method static string numeral($number, int $decimals = 0, ?string $dec_point = null, ?string $thousands_sep = null)
 * @method static string colorize(string $text)
 */
class Format
{
    use StaticTrait;
}

\class_alias('Lotgd\Core\Fixed\Format', 'LotgdFormat', false);
