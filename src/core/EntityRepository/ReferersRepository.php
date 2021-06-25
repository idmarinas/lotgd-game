<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\ReferersRepository as Core;

class_exists('Lotgd\Core\Repository\ReferersRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\ReferersRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\ReferersRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\ReferersRepository. Removed in 6.0.0 version. */
class ReferersRepository extends Core
{
}
