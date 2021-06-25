<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\FaillogRepository as Core;

class_exists('Lotgd\Core\Repository\FaillogRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\FaillogRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\FaillogRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\FaillogRepository. Removed in 6.0.0 version. */
class FaillogRepository extends Core
{
}
