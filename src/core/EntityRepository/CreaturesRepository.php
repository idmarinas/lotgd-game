<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\CreaturesRepository as Core;

class_exists('Lotgd\Core\Repository\CreaturesRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\CreaturesRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\CreaturesRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\CreaturesRepository. Removed in 6.0.0 version. */
class CreaturesRepository extends Core
{
}
