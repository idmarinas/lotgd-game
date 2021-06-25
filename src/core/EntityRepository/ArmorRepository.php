<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\ArmorRepository as Core;

class_exists('Lotgd\Core\Repository\ArmorRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\ArmorRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\ArmorRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\ArmorRepository. Removed in 6.0.0 version. */
class ArmorRepository extends Core
{
}
