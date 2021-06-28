<?php

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Repository\CommentaryRepository as Core;

class_exists('Lotgd\Core\Repository\CommentaryRepository');

@trigger_error('Using the "Lotgd\Core\EntityRepository\CommentaryRepository" class is deprecated since 5.5.0, use "Lotgd\Core\Repository\CommentaryRepository" instead.', \E_USER_DEPRECATED);

/** @deprecated since 5.5.0 Use Lotgd\Core\Repository\CommentaryRepository. Removed in 6.0.0 version. */
class CommentaryRepository extends Core
{
}
