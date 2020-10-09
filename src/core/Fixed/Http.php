<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Fixed;

use const E_USER_DEPRECATED;

\trigger_error(\sprintf(
    'Class %s is deprecated, please use %s instead',
    Http::class,
    Request::class
), E_USER_DEPRECATED);

/**
 * @deprecated since 4.4.0; to be removed in 5.0.0. Use Lotgd\Core\Http\Request instead.
 */
class Http extends Request
{
    public static function __callStatic($name, $arguments)
    {
        \trigger_error('Usage of LotgdHttp is deprecated, please use LotgdRequest instead', E_USER_DEPRECATED);

        return parent::__callStatic($name, $arguments);
    }
}

\class_alias('Lotgd\Core\Fixed\Http', 'LotgdHttp', false);
