<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\ModulesRepository as Core;

class_exists('Lotgd\Core\Repository\ModulesRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\ModulesRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\ModulesRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\ModulesRepository. Removed in 6.0.0 version. */
class ModulesRepository extends Core
{
}
