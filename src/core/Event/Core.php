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

class Core extends Event
{
    // Check login. Old: check-login
    public const LOGIN_CHECK = 'lotgd.core.login.check';

    // Player login. Old: player-login
    public const LOGIN_PLAYER = 'lotgd.core.login.player';

    // Player logout. Old: player-logout
    public const LOGOUT_PLAYER = 'lotgd.core.logout.player';

    // Add petition. Old: addpetition
    public const PETITION_ADD = 'lotgd.core.pettion.add';

    // Petition faq toc. Old: faq-toc
    public const PETITION_FAQ_TOC = 'lotgd.core.pettion.faq.toc';

    // Change setting
    public const SETTING_CHANGE = 'lotgd.core.setting.change';

    // Specialty names: Old: specialtynames
    public const SPECIALTY_NAMES = 'lotgd.core.specialty.names';

    // DK point recalc. Old: pdkpointrecalc
    public const DK_POINT_RECALC = 'lotgd.core.pdk.point.recalc';

    // New day run once. Old: newday-runonce
    public const NEWDAY_RUNONCE = 'lotgd.core.newday.runonce';

    // Pre new day. Old: pre-newday
    public const NEWDAY_PRE = 'lotgd.core.newday.pre';

    // New day. Old: newday
    public const NEWDAY = 'lotgd.core.newday';

    // Set race. Old: setrace
    public const RACE_SET = 'lotgd.core.race.set';

    // Choose race. Old: chooserace
    public const RACE_CHOOSE = 'lotgd.core.race.choose';

    // Set specialty. Old: set-specialty
    public const SPECIALTY_SET = 'lotgd.core.specialty.set';

    // Choose specialty. Old: choose-specialty
    public const SPECIALTY_CHOOSE = 'lotgd.core.specialty.choose';

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
