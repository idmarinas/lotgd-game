<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core;

use const E_USER_DEPRECATED;
use Lotgd\Core\Http\Request;

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
}
