<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\PaylogRepository as Core;

class_exists('Lotgd\Core\Repository\PaylogRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\PaylogRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\PaylogRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\PaylogRepository. Removed in 6.0.0 version. */
class PaylogRepository extends Core
{
}
