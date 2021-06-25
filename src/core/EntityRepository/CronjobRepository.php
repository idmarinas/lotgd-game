<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\CronjobRepository as Core;

class_exists('Lotgd\Core\Repository\CronjobRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\CronjobRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\CronjobRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\CronjobRepository. Removed in 6.0.0 version. */
class CronjobRepository extends Core
{
}
