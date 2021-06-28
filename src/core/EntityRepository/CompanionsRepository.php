<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\CompanionsRepository as Core;

class_exists('Lotgd\Core\Repository\CompanionsRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\CompanionsRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\CompanionsRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\CompanionsRepository. Removed in 6.0.0 version. */
class CompanionsRepository extends Core
{
}
