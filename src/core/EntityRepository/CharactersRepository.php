<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\CharactersRepository as Core;

class_exists('Lotgd\Core\Repository\CharactersRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\CharactersRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\CharactersRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\CharactersRepository. Removed in 6.0.0 version. */
class CharactersRepository extends Core
{
}
