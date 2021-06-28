<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\LogdnetRepository as Core;

class_exists('Lotgd\Core\Repository\LogdnetRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\LogdnetRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\LogdnetRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\LogdnetRepository. Removed in 6.0.0 version. */
class LogdnetRepository extends Core
{
}
