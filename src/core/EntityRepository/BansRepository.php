<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\BansRepository as Core;

class_exists('Lotgd\Core\Repository\BansRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\BansRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\BansRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\BansRepository. Removed in 6.0.0 version. */
class BansRepository extends Core
{
}
