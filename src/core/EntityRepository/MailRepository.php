<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\MailRepository as Core;

class_exists('Lotgd\Core\Repository\MailRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\MailRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\MailRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\MailRepository. Removed in 6.0.0 version. */
class MailRepository extends Core
{
}
