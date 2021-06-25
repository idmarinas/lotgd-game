<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\NewsRepository as Core;

class_exists('Lotgd\Core\Repository\NewsRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\NewsRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\NewsRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\NewsRepository. Removed in 6.0.0 version. */
class NewsRepository extends Core
{
}
