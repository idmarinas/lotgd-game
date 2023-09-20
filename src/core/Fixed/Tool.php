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

/**
 * @method static void addNews(string $text, array $params = [], string $textDomain = 'partial_news', bool $hideFromBio = false)
 * @method static int expForNextLevel(int $curlevel, int $curdk)
 * @method static void saveUser(bool $updateLastOn = true, bool $regenSession = false)
 */
class Tool
{
    use StaticTrait;
}

\class_alias('Lotgd\Core\Fixed\Tool', 'LotgdTool', false);
