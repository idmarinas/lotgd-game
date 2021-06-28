<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\MotdRepository as Core;

class_exists('Lotgd\Core\Repository\MotdRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\MotdRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\MotdRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\MotdRepository. Removed in 6.0.0 version. */
class MotdRepository extends Core
{
}
