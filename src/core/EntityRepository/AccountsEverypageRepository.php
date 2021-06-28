<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\AccountsEverypageRepository as Core;

class_exists('Lotgd\Core\Repository\AccountsEverypageRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\AccountsEverypageRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\AccountsEverypageRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\AccountsEverypageRepository. Removed in 6.0.0 version. */
class AccountsEverypageRepository extends Core
{
}
