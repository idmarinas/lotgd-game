<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\ClansRepository as Core;

class_exists('Lotgd\Core\Repository\ClansRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\ClansRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\ClansRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\ClansRepository. Removed in 6.0.0 version. */
class ClansRepository extends Core
{
}
