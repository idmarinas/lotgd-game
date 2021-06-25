<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\MountsRepository as Core;

class_exists('Lotgd\Core\Repository\MountsRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\MountsRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\MountsRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\MountsRepository. Removed in 6.0.0 version. */
class MountsRepository extends Core
{
}
