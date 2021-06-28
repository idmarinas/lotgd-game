<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\WeaponsRepository as Core;

class_exists('Lotgd\Core\Repository\WeaponsRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\WeaponsRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\WeaponsRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\WeaponsRepository. Removed in 6.0.0 version. */
class WeaponsRepository extends Core
{
}
