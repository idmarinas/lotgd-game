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

/**
 * @method static void pageStart($title = null, $parameters = [], $textDomain = null, $locale = null)
 * @method static void pageTitle($message, $parameters = [], $textDomain = null, $locale = null)
 * @method static void pageAddContent($content)
 * @method static void pageEnd($saveUser = true)
 */
class Response
{
    use StaticTrait;
}

\class_alias('Lotgd\Core\Fixed\Response', 'LotgdResponse', false);
