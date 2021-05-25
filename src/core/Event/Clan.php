<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.2.0
 */

namespace Lotgd\Core\Event;

use Symfony\Contracts\EventDispatcher\Event;

class Clan extends Event
{
    // Delete clan
    public const DELETE = 'lotgd.clan.delete';

    // Create clan
    public const CREATE = 'lotgd.clan.create';

    // Enter clan
    public const ENTER = 'lotgd.clan.enter';

    // Set clan rank
    public const RANK_SET = 'lotgd.clan.rank.set';

    // List clan rank
    public const RANK_LIST = 'lotgd.clan.rank.list';

    // Withdraw from clan
    public const WITHDRAW = 'lotgd.clan.withdraw';

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
