<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\PetitionsRepository as Core;

class_exists('Lotgd\Core\Repository\PetitionsRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\PetitionsRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\PetitionsRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\PetitionsRepository. Removed in 6.0.0 version. */
class PetitionsRepository extends Core
{
}
