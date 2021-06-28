<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\SettingsRepository as Core;

class_exists('Lotgd\Core\Repository\SettingsRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\SettingsRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\SettingsRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\SettingsRepository. Removed in 6.0.0 version. */
class SettingsRepository extends Core
{
}
