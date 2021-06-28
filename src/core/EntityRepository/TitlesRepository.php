<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\TitlesRepository as Core;

class_exists('Lotgd\Core\Repository\TitlesRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\TitlesRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\TitlesRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\TitlesRepository. Removed in 6.0.0 version. */
class TitlesRepository extends Core
{
}
