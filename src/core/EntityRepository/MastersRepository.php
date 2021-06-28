<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\MastersRepository as Core;

class_exists('Lotgd\Core\Repository\MastersRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\MastersRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\MastersRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\MastersRepository. Removed in 6.0.0 version. */
class MastersRepository extends Core
{
}
