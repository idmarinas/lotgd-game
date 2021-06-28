<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\ModuleUserprefsRepository as Core;

class_exists('Lotgd\Core\Repository\ModuleUserprefsRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\ModuleUserprefsRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\ModuleUserprefsRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\ModuleUserprefsRepository. Removed in 6.0.0 version. */
class ModuleUserprefsRepository extends Core
{
}
