<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Entity\Common;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

trait Deletable
{
    use SoftDeleteableEntity;

    public function isDeleted(): bool
    {
        return (bool) $this->getDeletedAt();
    }
}
