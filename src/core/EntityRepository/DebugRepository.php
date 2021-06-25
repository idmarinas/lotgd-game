<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\DebugRepository as Core;

class_exists('Lotgd\Core\Repository\DebugRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\DebugRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\DebugRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\DebugRepository. Removed in 6.0.0 version. */
class DebugRepository extends Core
{
}
