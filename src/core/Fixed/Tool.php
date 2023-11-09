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
 * @method static void addNews(string $text, array $params = [], string $textDomain = 'partial_news', bool $hideFromBio = false)
 * @method static int expForNextLevel(int $curlevel, int $curdk)
 * @method static void saveUser(bool $updateLastOn = true, bool $regenSession = false)
 * @method static array selectTaunt(array $extraParams = [])
 * @method static string getPartner(bool $player = false)
 * @method static array getMount($horse = 0)
 * @method static string changePlayerTitle($ntitle, $old = null)
 * @method static string getPlayerTitle($old = null)
 * @method static string getPlayerBasename($old = null)
 * @method static string getPlayerCBasename($old = null)
 * @method static string changePlayerName($newname, $old = null)
 * @method static string changePlayerCtitle($nctitle, $old = false)
 * @method static array selectDeathMessage(string $zone = 'forest', array $extraParams = [])
 * @method static void checkBan(?string $login = null)
 * @method static string substitute(?string $string, ?array $extraSearch = null, ?array $extraReplace = null)
 */
class Tool
{
    use StaticTrait;
}

class_alias('Lotgd\Core\Fixed\Tool', 'LotgdTool', false);
