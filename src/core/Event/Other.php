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

class Other extends Event
{
    // Check su permission. Old: stamina-newday
    public const STAMINA_NEWDAY = 'lotgd.other.stamina.newday';

    // Locations. Old: camplocs
    public const LOCATIONS = 'lotgd.other.locations';

    // End of bio page. Old: bioend
    public const BIO_END = 'lotgd.other.bio.end';

    // Special holiday
    public const SPECIAL_HOLIDAY = 'lotgd.other.holiday';

    // Dragonpoints reset
    public const SERVER_DRAGON_POINT_RESET = 'lotgd.other.server.dragon.point.reset';

    // Alter companion. Old: alter-companion
    public const COMPANION_ALTER = 'lotgd.companion.alter';

    // Bribe bartender
    public const INN_BARTENDER_BRIBE = 'lotgd.inn.bartender.bribe';

    // Ale
    public const INN_ALE = 'lotgd.inn.ale';

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
