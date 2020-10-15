<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Template;

\trigger_error(\sprintf(
    'Class %s is deprecated, please use %s instead',
    Base::class,
    Template::class
), E_USER_DEPRECATED);

class Base extends Template
{
}
