<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\AccountsRepository as Core;

class_exists('Lotgd\Core\Repository\AccountsRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\AccountsRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\AccountsRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\AccountsRepository. Removed in 6.0.0 version. */
class AccountsRepository extends Core
{
}
