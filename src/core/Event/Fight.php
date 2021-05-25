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

class Fight extends Event
{
    // Nav PRE
    public const NAV_PRE = 'lotgd.fight.nav.pre';

    // Nav Graveyard
    public const NAV_GRAVEYARD = 'lotgd.fight.nav.graveyard';

    // Nav Specialties
    public const NAV_SPECIALTY = 'lotgd.fight.nav.specialty';

    // Nav
    public const NAV = 'lotgd.fight.nav';

    // Options
    public const OPTIONS = 'lotgd.fight.options';

    // Alter gem chance
    public const ALTER_GEM_CHANCE = 'lotgd.fight.alter.gem.chance';

    // Apply specialties
    public const APPLY_SPECIALTY = 'lotgd.fight.apply.specialty';

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
