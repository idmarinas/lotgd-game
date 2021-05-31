<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.2.0
 */

namespace Lotgd\Core\Event;

use Symfony\Contracts\EventDispatcher\Event;

class Superuser extends Event
{
    // Superuser
    public const SUPERUSER = 'lotgd.other.superuser';

    // Check su access
    public const CHECK_SU_ACCESS = 'lotgd.other.superuser.check.su.access';

    // Check su permission
    public const CHECK_SU_PERMISSION = 'lotgd.other.superuser.check.su.permission';

    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
