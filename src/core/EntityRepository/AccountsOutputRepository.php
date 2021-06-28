<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\AccountsOutputRepository as Core;

class_exists('Lotgd\Core\Repository\AccountsOutputRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\AccountsOutputRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\AccountsOutputRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\AccountsOutputRepository. Removed in 6.0.0 version. */
class AccountsOutputRepository extends Core
{
}
