<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\WhostypingRepository as Core;

class_exists('Lotgd\Core\Repository\WhostypingRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\WhostypingRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\WhostypingRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\WhostypingRepository. Removed in 6.0.0 version. */
class WhostypingRepository extends Core
{
}
