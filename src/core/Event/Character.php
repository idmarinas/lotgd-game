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

class Character extends Event
{
    // End of generated characters stats
    public const STATS = 'lotgd.character.stats';

    // When generated characters stats, but character is offline
    public const ONLINE_LIST = 'lotgd.character.online.list';

    // Modify character buff
    public const MODIFY_BUFF = 'lotgd.character.modify.buff';

    // Character companions
    public const COMPANIONS_ALLOWED = 'lotgd.character.companions.allowed';

    // Character cleanup
    public const CLEANUP = 'lotgd.character.cleanup';

    // Character killed player
    public const KILLED_PLAYER = 'lotgd.character.killed.player';

    // Character increment specialty
    public const SPECIALTY_INCREMENT = 'lotgd.character.specialty.increment';

    // Character pvp adjust
    public const PVP_ADJUST = 'lotgd.character.pvp.adjust';

    // Character pvp win
    public const PVP_WIN = 'lotgd.character.pvp.win';

    // Character pvp loss
    public const PVP_LOSS = 'lotgd.character.pvp.loss';

    // Character pvp do kill
    public const PVP_DO_KILL = 'lotgd.character.pvp.do.kill';

    // Character restore backup. Old: character-restore
    public const BACKUP_RESTORE = 'lotgd.character.backup.restore';

    // Character races names. Old: racenames
    public const RACE_NAMES = 'lotgd.character.race.names';

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
