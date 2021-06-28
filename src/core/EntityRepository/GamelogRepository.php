<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\GamelogRepository as Core;

class_exists('Lotgd\Core\Repository\GamelogRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\GamelogRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\GamelogRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\GamelogRepository. Removed in 6.0.0 version. */
class GamelogRepository extends Core
{
}
